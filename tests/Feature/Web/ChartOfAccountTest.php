<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartOfAccountTest extends TestCase
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
        ChartOfAccount::factory(3)->create();

        $this->actingAs($this->user)
            ->get(route('admin.finance.accounts'))
            ->assertOk();
    }

    public function test_create(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.finance.coa.create'))
            ->assertOk();
    }

    public function test_store(): void
    {
        $this->actingAs($this->user)
            ->post(route('admin.finance.coa.store'), [
                'code' => '1101',
                'name' => 'Kas',
                'category' => 'asset',
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.finance.accounts'));

        $this->assertDatabaseHas('chart_of_accounts', [
            'code' => '1101',
            'name' => 'Kas',
        ]);
    }

    public function test_edit(): void
    {
        $account = ChartOfAccount::factory()->create();

        $this->actingAs($this->user)
            ->get(route('admin.finance.coa.edit', $account))
            ->assertOk();
    }

    public function test_update(): void
    {
        $account = ChartOfAccount::factory()->create(['name' => 'Old Account']);

        $this->actingAs($this->user)
            ->put(route('admin.finance.coa.update', $account), [
                'code' => $account->code,
                'name' => 'Updated Account',
                'category' => 'asset',
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.finance.accounts'));

        $this->assertEquals('Updated Account', $account->fresh()->name);
    }

    public function test_destroy(): void
    {
        $account = ChartOfAccount::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('admin.finance.coa.destroy', $account))
            ->assertRedirect(route('admin.finance.accounts'));

        $this->assertSoftDeleted($account);
    }
}
