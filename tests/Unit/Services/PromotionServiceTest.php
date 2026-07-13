<?php

namespace Tests\Unit\Services;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\PromotionUsage;
use App\Services\PromotionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionServiceTest extends TestCase
{
    use RefreshDatabase;

    private PromotionService $service;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PromotionService::class);
        $this->branch = Branch::factory()->create();
    }

    public function test_get_eligible_promotions_returns_active_within_date_range(): void
    {
        Promotion::factory()->create([
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        $eligible = $this->service->getEligiblePromotions();

        $this->assertCount(1, $eligible);
    }

    public function test_get_eligible_promotions_excludes_expired(): void
    {
        Promotion::factory()->create([
            'start_date' => now()->subDays(20),
            'end_date' => now()->subDays(5),
            'is_active' => true,
        ]);

        $eligible = $this->service->getEligiblePromotions();

        $this->assertCount(0, $eligible);
    }

    public function test_get_eligible_promotions_excludes_inactive(): void
    {
        Promotion::factory()->create([
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
            'is_active' => false,
        ]);

        $eligible = $this->service->getEligiblePromotions();

        $this->assertCount(0, $eligible);
    }

    public function test_get_eligible_promotions_filters_by_min_order(): void
    {
        Promotion::factory()->create([
            'min_order_amount' => 50000,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        $eligible = $this->service->getEligiblePromotions(null, 30000);

        $this->assertCount(0, $eligible);
    }

    public function test_get_eligible_promotions_passes_min_order_check(): void
    {
        Promotion::factory()->create([
            'min_order_amount' => 50000,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        $eligible = $this->service->getEligiblePromotions(null, 75000);

        $this->assertCount(1, $eligible);
    }

    public function test_get_eligible_promotions_filters_by_usage_limit(): void
    {
        $promotion = Promotion::factory()->create([
            'total_usage_limit' => 5,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        PromotionUsage::factory()->count(5)->create([
            'promotion_id' => $promotion->id,
        ]);

        $eligible = $this->service->getEligiblePromotions();

        $this->assertCount(0, $eligible);
    }

    public function test_get_eligible_promotions_filters_by_branch(): void
    {
        $otherBranch = Branch::factory()->create();

        $promotion = Promotion::factory()->create([
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        $promotion->branches()->attach($otherBranch->id);

        $eligible = $this->service->getEligiblePromotions($this->branch->id);

        $this->assertCount(0, $eligible);
    }

    public function test_get_eligible_promotions_returns_all_branches_when_no_branch_restrictions(): void
    {
        Promotion::factory()->create([
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        $eligible = $this->service->getEligiblePromotions($this->branch->id);

        $this->assertCount(1, $eligible);
    }

    public function test_get_eligible_promotions_matches_assigned_branch(): void
    {
        $promotion = Promotion::factory()->create([
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        $promotion->branches()->attach($this->branch->id);

        $eligible = $this->service->getEligiblePromotions($this->branch->id);

        $this->assertCount(1, $eligible);
    }

    public function test_toggle_branch_creates_new_record(): void
    {
        $promotion = Promotion::factory()->create();

        $result = $this->service->toggleBranch($promotion, $this->branch);

        $this->assertTrue($result->is_active);
        $this->assertDatabaseHas('promotion_branches', [
            'promotion_id' => $promotion->id,
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);
    }

    public function test_toggle_branch_toggles_existing_record(): void
    {
        $promotion = Promotion::factory()->create();
        $promotion->branches()->attach($this->branch->id, ['is_active' => true]);

        $result = $this->service->toggleBranch($promotion, $this->branch);

        $this->assertFalse($result->is_active);
    }

    public function test_record_usage_creates_usage(): void
    {
        $promotion = Promotion::factory()->create();
        $order = Order::factory()->create();
        $customer = Customer::factory()->create();

        $usage = $this->service->recordUsage($promotion, $order->id, $customer->id, 10000);

        $this->assertInstanceOf(PromotionUsage::class, $usage);
        $this->assertSame($promotion->id, $usage->promotion_id);
        $this->assertSame($order->id, $usage->order_id);
        $this->assertSame($customer->id, $usage->customer_id);
        $this->assertSame(10000.0, $usage->discount_amount);
    }

    public function test_get_usage_count_returns_zero_when_no_usage(): void
    {
        $promotion = Promotion::factory()->create();

        $count = $this->service->getUsageCount($promotion);

        $this->assertSame(0, $count);
    }

    public function test_get_usage_count_returns_total(): void
    {
        $promotion = Promotion::factory()->create();
        PromotionUsage::factory()->count(3)->create([
            'promotion_id' => $promotion->id,
        ]);

        $count = $this->service->getUsageCount($promotion);

        $this->assertSame(3, $count);
    }

    public function test_get_usage_count_filters_by_customer(): void
    {
        $promotion = Promotion::factory()->create();
        $customer = Customer::factory()->create();

        PromotionUsage::factory()->count(2)->create([
            'promotion_id' => $promotion->id,
            'customer_id' => $customer->id,
        ]);

        PromotionUsage::factory()->count(3)->create([
            'promotion_id' => $promotion->id,
        ]);

        $count = $this->service->getUsageCount($promotion, $customer->id);

        $this->assertSame(2, $count);
    }
}
