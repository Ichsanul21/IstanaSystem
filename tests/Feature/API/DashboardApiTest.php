<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardApiTest extends TestCase
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

    public function test_summary_returns_metrics(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/dashboard/summary')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'metrics',
                    'revenue_trend',
                    'order_status',
                    'top_services',
                ],
            ]);
    }

    public function test_revenue_returns_revenue_data(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/dashboard/revenue')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'by_service',
                    'by_branch',
                    'trend',
                ],
            ]);
    }

    public function test_production_returns_production_data(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/dashboard/production')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'queue_by_status',
                    'average_processing_time',
                    'items_in_production',
                ],
            ]);
    }

    public function test_finance_returns_finance_data(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/dashboard/finance')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'revenue_vs_expense',
                    'profit_margin',
                    'monthly_trend',
                ],
            ]);
    }

    public function test_unauthenticated_access_is_blocked(): void
    {
        $this->getJson('/api/v1/dashboard/summary')
            ->assertUnauthorized();
    }
}
