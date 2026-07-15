<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
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

    public function test_index(): void
    {
        Expense::create([
            'branch_id' => $this->branch->id,
            'created_by' => $this->user->id,
            'category' => 'Operasional',
            'description' => 'Beli deterjen',
            'amount' => 150000,
            'posted_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->get(route('admin.finance.expenses.index'))
            ->assertOk();
    }

    public function test_create(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.finance.expenses.create'))
            ->assertOk();
    }

    public function test_store(): void
    {
        $this->actingAs($this->user)
            ->post(route('admin.finance.expenses.store'), [
                'category' => 'Operasional',
                'description' => 'Beli deterjen 10kg',
                'amount' => 150000,
                'posted_at' => now()->format('Y-m-d'),
            ])
            ->assertRedirect(route('admin.finance.expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'category' => 'Operasional',
            'description' => 'Beli deterjen 10kg',
            'amount' => 150000,
            'branch_id' => $this->branch->id,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_edit(): void
    {
        $expense = Expense::create([
            'branch_id' => $this->branch->id,
            'created_by' => $this->user->id,
            'category' => 'Operasional',
            'description' => 'Beli deterjen',
            'amount' => 150000,
            'posted_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->get(route('admin.finance.expenses.edit', $expense))
            ->assertOk();
    }

    public function test_update(): void
    {
        $expense = Expense::create([
            'branch_id' => $this->branch->id,
            'created_by' => $this->user->id,
            'category' => 'Operasional',
            'description' => 'Beli deterjen',
            'amount' => 150000,
            'posted_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->put(route('admin.finance.expenses.update', $expense), [
                'category' => 'Utilitas',
                'description' => 'Bayar listrik',
                'amount' => 500000,
                'posted_at' => now()->format('Y-m-d'),
            ])
            ->assertRedirect(route('admin.finance.expenses.index'));

        $this->assertEquals('Bayar listrik', $expense->fresh()->description);
        $this->assertEquals(500000, $expense->fresh()->amount);
    }

    public function test_destroy(): void
    {
        $expense = Expense::create([
            'branch_id' => $this->branch->id,
            'created_by' => $this->user->id,
            'category' => 'Operasional',
            'description' => 'Beli deterjen',
            'amount' => 150000,
            'posted_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->delete(route('admin.finance.expenses.destroy', $expense))
            ->assertRedirect(route('admin.finance.expenses.index'));

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }
}
