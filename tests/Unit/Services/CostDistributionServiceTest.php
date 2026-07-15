<?php

namespace Tests\Unit\Services;

use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use App\Models\Workshop;
use App\Services\Finance\CostDistributionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CostDistributionServiceTest extends TestCase
{
    use RefreshDatabase;

    private CostDistributionService $service;
    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CostDistributionService::class);
        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->actingAs($this->user);
    }

    public function test_distribute_workshop_cost_returns_empty_when_no_branches(): void
    {
        $workshop = Workshop::factory()->create();

        $result = $this->service->distributeWorkshopCost(100000, $workshop->id, '2026-07');

        $this->assertEmpty($result);
    }

    public function test_distribute_workshop_cost_distributes_equally_when_no_orders(): void
    {
        $workshop = Workshop::factory()->create();
        $branch1 = Branch::factory()->create(['workshop_id' => $workshop->id]);
        $branch2 = Branch::factory()->create(['workshop_id' => $workshop->id]);

        $result = $this->service->distributeWorkshopCost(100000, $workshop->id, (string) date('Y'));

        $this->assertCount(2, $result);
        $this->assertEquals(50000.0, $result[$branch1->id]);
        $this->assertEquals(50000.0, $result[$branch2->id]);
    }

    public function test_distribute_workshop_cost_distributes_proportionally_by_order_count(): void
    {
        $workshop = Workshop::factory()->create();
        $branch1 = Branch::factory()->create(['workshop_id' => $workshop->id]);
        $branch2 = Branch::factory()->create(['workshop_id' => $workshop->id]);

        $now = now();
        for ($i = 0; $i < 3; $i++) {
            Order::factory()->create(['branch_id' => $branch1->id, 'created_at' => $now]);
        }
        for ($i = 0; $i < 1; $i++) {
            Order::factory()->create(['branch_id' => $branch2->id, 'created_at' => $now]);
        }

        $result = $this->service->distributeWorkshopCost(100000, $workshop->id, (string) date('Y'));

        $this->assertCount(2, $result);
        $this->assertEquals(75000.0, $result[$branch1->id]);
        $this->assertEquals(25000.0, $result[$branch2->id]);
    }

    public function test_distribute_workshop_cost_only_counts_branches_under_same_workshop(): void
    {
        $workshop1 = Workshop::factory()->create();
        $workshop2 = Workshop::factory()->create();
        $branchA = Branch::factory()->create(['workshop_id' => $workshop1->id]);
        $branchB = Branch::factory()->create(['workshop_id' => $workshop1->id]);
        $branchC = Branch::factory()->create(['workshop_id' => $workshop2->id]);

        $now = now();
        Order::factory()->create(['branch_id' => $branchA->id, 'created_at' => $now]);
        Order::factory()->create(['branch_id' => $branchA->id, 'created_at' => $now]);
        Order::factory()->create(['branch_id' => $branchC->id, 'created_at' => $now]);

        $result = $this->service->distributeWorkshopCost(60000, $workshop1->id, (string) date('Y'));

        $this->assertCount(2, $result);
        $this->assertArrayNotHasKey($branchC->id, $result);
        $this->assertEquals(60000.0, $result[$branchA->id]);
        $this->assertEquals(0.0, $result[$branchB->id]);
    }

    public function test_distribute_workshop_cost_distributes_equally_for_three_branches_with_no_orders(): void
    {
        $workshop = Workshop::factory()->create();
        $branch1 = Branch::factory()->create(['workshop_id' => $workshop->id]);
        $branch2 = Branch::factory()->create(['workshop_id' => $workshop->id]);
        $branch3 = Branch::factory()->create(['workshop_id' => $workshop->id]);

        $result = $this->service->distributeWorkshopCost(90000, $workshop->id, (string) date('Y'));

        $this->assertCount(3, $result);
        $this->assertEquals(30000.0, $result[$branch1->id]);
        $this->assertEquals(30000.0, $result[$branch2->id]);
        $this->assertEquals(30000.0, $result[$branch3->id]);
    }
}
