<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceAuthTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->session(['current_branch_id' => $this->branch->id]);
    }

    public function test_finance_index_requires_finance_read(): void
    {
        $this->user->givePermissionTo('finance.read');
        $response = $this->actingAs($this->user)->get(route('admin.finance.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('finance.read');
        $this->actingAs($this->user)->get(route('admin.finance.index'))->assertForbidden();
    }

    public function test_finance_accounts_requires_finance_read(): void
    {
        $this->user->givePermissionTo('finance.read');
        $response = $this->actingAs($this->user)->get(route('admin.finance.accounts'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('finance.read');
        $this->actingAs($this->user)->get(route('admin.finance.accounts'))->assertForbidden();
    }

    public function test_finance_journal_requires_finance_read(): void
    {
        $this->user->givePermissionTo('finance.read');
        $response = $this->actingAs($this->user)->get(route('admin.finance.journal'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('finance.read');
        $this->actingAs($this->user)->get(route('admin.finance.journal'))->assertForbidden();
    }

    public function test_finance_trial_balance_requires_finance_read(): void
    {
        $this->user->givePermissionTo('finance.read');
        $response = $this->actingAs($this->user)->get(route('admin.finance.trial-balance'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('finance.read');
        $this->actingAs($this->user)->get(route('admin.finance.trial-balance'))->assertForbidden();
    }

    public function test_finance_income_statement_requires_finance_read(): void
    {
        $this->user->givePermissionTo('finance.read');
        $response = $this->actingAs($this->user)->get(route('admin.finance.income-statement'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('finance.read');
        $this->actingAs($this->user)->get(route('admin.finance.income-statement'))->assertForbidden();
    }

    public function test_finance_create_journal_requires_create_manual_journal(): void
    {
        $this->user->givePermissionTo('create_manual_journal');
        $response = $this->actingAs($this->user)->get(route('admin.finance.journal.create'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('create_manual_journal');
        $this->actingAs($this->user)->get(route('admin.finance.journal.create'))->assertForbidden();
    }

    public function test_finance_coa_create_requires_finance_read(): void
    {
        $this->user->givePermissionTo('finance.read');
        $response = $this->actingAs($this->user)->get(route('admin.finance.coa.create'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('finance.read');
        $this->actingAs($this->user)->get(route('admin.finance.coa.create'))->assertForbidden();
    }

    public function test_finance_expense_index_requires_finance_read(): void
    {
        $this->user->givePermissionTo('finance.read');
        $response = $this->actingAs($this->user)->get(route('admin.finance.expenses.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('finance.read');
        $this->actingAs($this->user)->get(route('admin.finance.expenses.index'))->assertForbidden();
    }

    public function test_finance_manage_expenses_requires_manage_expenses(): void
    {
        $this->user->givePermissionTo('manage_expenses');
        $response = $this->actingAs($this->user)->get(route('admin.finance.expenses.create'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('manage_expenses');
        $this->actingAs($this->user)->get(route('admin.finance.expenses.create'))->assertForbidden();
    }

    public function test_finance_period_index_requires_finance_read(): void
    {
        $this->user->givePermissionTo('finance.read');
        $response = $this->actingAs($this->user)->get(route('admin.finance.periods.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('finance.read');
        $this->actingAs($this->user)->get(route('admin.finance.periods.index'))->assertForbidden();
    }

    public function test_finance_manage_periods_requires_manage_accounting_periods(): void
    {
        $this->user->givePermissionTo('manage_accounting_periods');
        $response = $this->actingAs($this->user)->get(route('admin.finance.periods.create'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('manage_accounting_periods');
        $this->actingAs($this->user)->get(route('admin.finance.periods.create'))->assertForbidden();
    }
}
