<?php

namespace Tests\Unit\Services;

use App\Models\AccountingPeriod;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\User;
use App\Services\Finance\JournalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalServiceTest extends TestCase
{
    use RefreshDatabase;

    private JournalService $service;
    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(JournalService::class);
        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->actingAs($this->user);

        AccountingPeriod::factory()->create([
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_closed' => false,
        ]);
    }

    public function test_create_entry_creates_journal_entry_with_balanced_lines(): void
    {
        $debitAccount = ChartOfAccount::factory()->create(['category' => 'asset']);
        $creditAccount = ChartOfAccount::factory()->create(['category' => 'revenue']);

        $entry = $this->service->createEntry('Test journal entry', [
            ['account_id' => $debitAccount->id, 'debit' => 50000, 'credit' => 0, 'description' => 'Debit line'],
            ['account_id' => $creditAccount->id, 'debit' => 0, 'credit' => 50000, 'description' => 'Credit line'],
        ], $this->branch->id);

        $this->assertInstanceOf(JournalEntry::class, $entry);
        $this->assertSame('Test journal entry', $entry->description);
        $this->assertSame($this->branch->id, $entry->branch_id);

        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'account_id' => $debitAccount->id,
            'debit' => 50000,
            'credit' => 0,
        ]);

        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'account_id' => $creditAccount->id,
            'debit' => 0,
            'credit' => 50000,
        ]);
    }

    public function test_create_entry_accepts_type_reference_params(): void
    {
        $account = ChartOfAccount::factory()->create(['category' => 'asset']);

        $entry = $this->service->createEntry('Payment journal', [
            ['account_id' => $account->id, 'debit' => 25000, 'credit' => 0],
            ['account_id' => $account->id, 'debit' => 0, 'credit' => 25000],
        ], $this->branch->id, 'cogs', 'App\\Models\\Order', 42);

        $this->assertSame('cogs', $entry->type);
        $this->assertSame('App\\Models\\Order', $entry->reference_type);
        $this->assertSame(42, $entry->reference_id);
    }

    public function test_create_entry_throws_on_unbalanced_lines(): void
    {
        $account = ChartOfAccount::factory()->create(['category' => 'asset']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Debit and credit totals must be equal.');

        $this->service->createEntry('Unbalanced entry', [
            ['account_id' => $account->id, 'debit' => 50000, 'credit' => 0],
            ['account_id' => $account->id, 'debit' => 0, 'credit' => 30000],
        ], $this->branch->id);
    }

    public function test_create_entry_throws_when_difference_exceeds_threshold(): void
    {
        $account = ChartOfAccount::factory()->create(['category' => 'asset']);

        $this->expectException(\InvalidArgumentException::class);

        $this->service->createEntry('Slightly unbalanced', [
            ['account_id' => $account->id, 'debit' => 50000, 'credit' => 0],
            ['account_id' => $account->id, 'debit' => 0, 'credit' => 49999],
        ], $this->branch->id);
    }

    public function test_create_entry_sets_type_manual_when_not_provided(): void
    {
        $account = ChartOfAccount::factory()->create(['category' => 'asset']);

        $entry = $this->service->createEntry('Manual entry', [
            ['account_id' => $account->id, 'debit' => 10000, 'credit' => 0],
            ['account_id' => $account->id, 'debit' => 0, 'credit' => 10000],
        ], $this->branch->id);

        $this->assertSame('manual', $entry->type);
    }

    public function test_get_account_balance_returns_correct_balance_for_debit_norm_accounts(): void
    {
        $account = ChartOfAccount::factory()->create(['category' => 'asset']);

        $debitAccount = ChartOfAccount::factory()->create(['category' => 'asset']);
        $creditAccount = ChartOfAccount::factory()->create(['category' => 'revenue']);

        $this->service->createEntry('Debit 10000', [
            ['account_id' => $account->id, 'debit' => 10000, 'credit' => 0],
            ['account_id' => $creditAccount->id, 'debit' => 0, 'credit' => 10000],
        ], $this->branch->id);

        $this->service->createEntry('Credit 3000', [
            ['account_id' => $account->id, 'debit' => 0, 'credit' => 3000],
            ['account_id' => $debitAccount->id, 'debit' => 3000, 'credit' => 0],
        ], $this->branch->id);

        $balance = $this->service->getAccountBalance($account->id, $this->branch->id);

        $this->assertEqualsWithDelta(7000, $balance, 0.01);
    }

    public function test_get_account_balance_returns_correct_balance_for_credit_norm_accounts(): void
    {
        $account = ChartOfAccount::factory()->create(['category' => 'revenue']);
        $debitAccount = ChartOfAccount::factory()->create(['category' => 'asset']);

        $this->service->createEntry('Revenue credit 20000', [
            ['account_id' => $account->id, 'debit' => 0, 'credit' => 20000],
            ['account_id' => $debitAccount->id, 'debit' => 20000, 'credit' => 0],
        ], $this->branch->id);

        $this->service->createEntry('Revenue debit 5000', [
            ['account_id' => $account->id, 'debit' => 5000, 'credit' => 0],
            ['account_id' => $debitAccount->id, 'debit' => 0, 'credit' => 5000],
        ], $this->branch->id);

        $balance = $this->service->getAccountBalance($account->id, $this->branch->id);

        $this->assertEqualsWithDelta(15000, $balance, 0.01);
    }

    public function test_get_trial_balance_returns_collection_with_all_active_accounts(): void
    {
        $active1 = ChartOfAccount::factory()->create(['is_active' => true, 'category' => 'asset']);
        $active2 = ChartOfAccount::factory()->create(['is_active' => true, 'category' => 'revenue']);
        ChartOfAccount::factory()->create(['is_active' => false, 'category' => 'expense']);

        $this->service->createEntry('Test entry', [
            ['account_id' => $active1->id, 'debit' => 10000, 'credit' => 0],
            ['account_id' => $active2->id, 'debit' => 0, 'credit' => 10000],
        ], $this->branch->id);

        $trialBalance = $this->service->getTrialBalance($this->branch->id);

        $this->assertCount(2, $trialBalance);

        $codes = $trialBalance->pluck('account_code')->values()->all();
        $this->assertContains($active1->code, $codes);
        $this->assertContains($active2->code, $codes);

        $entry1 = $trialBalance->firstWhere('account_code', $active1->code);
        $this->assertEqualsWithDelta(10000, $entry1['debit'], 0.01);
        $this->assertEquals(0, $entry1['credit']);
    }

    public function test_get_trial_balance_separates_debit_and_credit(): void
    {
        $asset = ChartOfAccount::factory()->create(['is_active' => true, 'category' => 'asset']);
        $revenue = ChartOfAccount::factory()->create(['is_active' => true, 'category' => 'revenue']);

        $this->service->createEntry('Mixed entry', [
            ['account_id' => $asset->id, 'debit' => 50000, 'credit' => 0],
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 50000],
        ], $this->branch->id);

        $trialBalance = $this->service->getTrialBalance($this->branch->id);

        $assetRow = $trialBalance->firstWhere('account_code', $asset->code);
        $this->assertEqualsWithDelta(50000, $assetRow['debit'], 0.01);
        $this->assertEquals(0, $assetRow['credit']);

        $revenueRow = $trialBalance->firstWhere('account_code', $revenue->code);
        $this->assertEquals(0, $revenueRow['debit']);
        $this->assertEqualsWithDelta(50000, $revenueRow['credit'], 0.01);
    }
}
