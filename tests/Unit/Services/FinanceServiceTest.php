<?php

namespace Tests\Unit\Services;

use App\Models\AccountingPeriod;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\User;
use App\Services\FinanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private FinanceService $service;
    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FinanceService::class);
        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->actingAs($this->user);
    }

    public function test_create_journal_entry_with_balanced_lines(): void
    {
        $accountDebit = ChartOfAccount::factory()->create(['code' => '1-1000', 'name' => 'Kas']);
        $accountCredit = ChartOfAccount::factory()->create(['code' => '4-1000', 'name' => 'Pendapatan']);
        AccountingPeriod::factory()->create([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'is_closed' => false,
        ]);

        $entry = $this->service->createJournalEntry(
            'Test entry',
            [
                ['account_id' => $accountDebit->id, 'debit' => 50000, 'credit' => 0],
                ['account_id' => $accountCredit->id, 'debit' => 0, 'credit' => 50000],
            ],
            $this->branch->id
        );

        $this->assertSame('Test entry', $entry->description);
        $this->assertSame($this->branch->id, $entry->branch_id);
        $this->assertMatchesRegularExpression('/^JE-\d{8}-\d{5}$/', $entry->entry_number);

        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 50000,
            'credit' => 0,
        ]);
        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'debit' => 0,
            'credit' => 50000,
        ]);
    }

    public function test_unbalanced_lines_throws_exception(): void
    {
        $accountDebit = ChartOfAccount::factory()->create();
        $accountCredit = ChartOfAccount::factory()->create();

        $this->expectException(\InvalidArgumentException::class);

        $this->service->createJournalEntry(
            'Unbalanced entry',
            [
                ['account_id' => $accountDebit->id, 'debit' => 50000, 'credit' => 0],
                ['account_id' => $accountCredit->id, 'debit' => 0, 'credit' => 30000],
            ]
        );
    }

    public function test_entry_number_format(): void
    {
        $account = ChartOfAccount::factory()->create();
        AccountingPeriod::factory()->create([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'is_closed' => false,
        ]);

        $entry = $this->service->createJournalEntry(
            'Number format test',
            [
                ['account_id' => $account->id, 'debit' => 10000, 'credit' => 0],
                ['account_id' => $account->id, 'debit' => 0, 'credit' => 10000],
            ]
        );

        $this->assertMatchesRegularExpression('/^JE-\d{8}-\d{5}$/', $entry->entry_number);
    }
}
