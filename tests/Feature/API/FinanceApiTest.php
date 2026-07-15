<?php

namespace Tests\Feature\Api;

use App\Models\AccountingPeriod;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceApiTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->assignRole('Developer');
        session(['current_branch_id' => $this->branch->id]);
    }

    public function test_coa_index_returns_accounts(): void
    {
        ChartOfAccount::factory(3)->create(['is_active' => true]);

        $this->actingAs($this->user)
            ->getJson('/api/v1/finance/coa')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_journal_index_returns_entries(): void
    {
        $period = AccountingPeriod::factory()->create();

        $this->actingAs($this->user)
            ->getJson('/api/v1/finance/journal')
            ->assertSuccessful()
            ->assertJsonStructure(['success']);
    }

    public function test_journal_store_creates_entry(): void
    {
        AccountingPeriod::factory()->create([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'is_closed' => false,
        ]);

        $account1 = ChartOfAccount::factory()->create(['is_active' => true]);
        $account2 = ChartOfAccount::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/finance/journal', [
                'description' => 'Test journal entry',
                'entry_date' => now()->toDateString(),
                'lines' => [
                    ['account_id' => $account1->id, 'debit' => 50000, 'credit' => 0],
                    ['account_id' => $account2->id, 'debit' => 0, 'credit' => 50000],
                ],
            ]);

        $response->assertCreated()
            ->assertJsonPath('success', true);
    }

    public function test_journal_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/v1/finance/journal', [
                'description' => '',
                'lines' => [],
            ])
            ->assertStatus(422);
    }

    public function test_trial_balance_returns_data(): void
    {
        ChartOfAccount::factory()->create(['is_active' => true]);

        $this->actingAs($this->user)
            ->getJson('/api/v1/finance/trial-balance')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_profit_loss_returns_data(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/finance/profit-loss')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'revenue',
                    'total_revenue',
                    'expenses',
                    'total_expenses',
                    'net_income',
                ],
            ]);
    }

    public function test_balance_sheet_returns_data(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/finance/balance-sheet')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'assets',
                    'liabilities',
                    'equity',
                ],
            ]);
    }

    public function test_coa_show_returns_account_detail(): void
    {
        $account = ChartOfAccount::factory()->create(['is_active' => true]);

        $this->actingAs($this->user)
            ->getJson("/api/v1/finance/coa/{$account->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $account->id)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'code', 'name', 'type', 'balance', 'is_active'],
            ]);
    }

    public function test_unauthenticated_access_is_blocked(): void
    {
        $this->getJson('/api/v1/finance/coa')
            ->assertUnauthorized();
    }
}
