<?php

namespace Tests\Feature\Web;

use App\Models\AccountingPeriod;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingPeriodTest extends TestCase
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
        AccountingPeriod::factory(3)->create();

        $this->actingAs($this->user)
            ->get(route('admin.finance.periods.index'))
            ->assertOk();
    }

    public function test_create(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.finance.periods.create'))
            ->assertOk();
    }

    public function test_store(): void
    {
        $this->actingAs($this->user)
            ->post(route('admin.finance.periods.store'), [
                'name' => 'Juli 2026',
                'start_date' => '2026-07-01',
                'end_date' => '2026-07-31',
                'is_closed' => false,
            ])
            ->assertRedirect(route('admin.finance.periods.index'));

        $this->assertDatabaseHas('accounting_periods', [
            'name' => 'Juli 2026',
            'is_closed' => false,
        ]);
    }

    public function test_edit(): void
    {
        $period = AccountingPeriod::factory()->create();

        $this->actingAs($this->user)
            ->get(route('admin.finance.periods.edit', $period))
            ->assertOk();
    }

    public function test_update(): void
    {
        $period = AccountingPeriod::factory()->create(['name' => 'Januari 2026']);

        $this->actingAs($this->user)
            ->put(route('admin.finance.periods.update', $period), [
                'name' => 'Januari 2026 Diperbarui',
                'start_date' => $period->start_date->format('Y-m-d'),
                'end_date' => $period->end_date->format('Y-m-d'),
                'is_closed' => false,
            ])
            ->assertRedirect(route('admin.finance.periods.index'));

        $this->assertEquals('Januari 2026 Diperbarui', $period->fresh()->name);
    }

    public function test_destroy(): void
    {
        $period = AccountingPeriod::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('admin.finance.periods.destroy', $period))
            ->assertRedirect(route('admin.finance.periods.index'));

        $this->assertDatabaseMissing('accounting_periods', ['id' => $period->id]);
    }
}
