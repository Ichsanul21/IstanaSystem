<?php

namespace Tests\Unit\Services\Dashboard;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\Dashboard\RevenueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RevenueServiceTest extends TestCase
{
    use RefreshDatabase;

    private RevenueService $service;
    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RevenueService::class);
        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->actingAs($this->user);
    }

    public function test_get_revenue_trend_returns_array_with_date_and_revenue_keys(): void
    {
        $result = $this->service->getRevenueTrend(7);

        $this->assertIsArray($result);
        $this->assertCount(7, $result);

        foreach ($result as $entry) {
            $this->assertArrayHasKey('date', $entry);
            $this->assertArrayHasKey('revenue', $entry);
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $entry['date']);
            $this->assertIsFloat($entry['revenue']);
        }
    }

    public function test_get_revenue_trend_includes_payments_for_current_day(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
        ]);
        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 75000,
            'paid_at' => now(),
        ]);

        $result = $this->service->getRevenueTrend(1);

        $this->assertCount(1, $result);
        $this->assertEquals(now()->format('Y-m-d'), $result[0]['date']);
        $this->assertEquals(75000.0, $result[0]['revenue']);
    }

    public function test_get_revenue_trend_filters_by_branch(): void
    {
        $otherBranch = Branch::factory()->create();

        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $order1 = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
        ]);
        Payment::factory()->create([
            'order_id' => $order1->id,
            'amount' => 50000,
            'paid_at' => now(),
        ]);

        $customer2 = Customer::factory()->create(['branch_id' => $otherBranch->id]);
        $order2 = Order::factory()->create([
            'branch_id' => $otherBranch->id,
            'customer_id' => $customer2->id,
        ]);
        Payment::factory()->create([
            'order_id' => $order2->id,
            'amount' => 100000,
            'paid_at' => now(),
        ]);

        $result = $this->service->getRevenueTrend(1, $this->branch->id);

        $this->assertEquals(50000.0, $result[0]['revenue']);
    }

    public function test_get_revenue_by_branch_returns_per_branch_revenue(): void
    {
        $branch1 = Branch::factory()->create(['is_active' => true]);
        $branch2 = Branch::factory()->create(['is_active' => true]);

        $customer1 = Customer::factory()->create(['branch_id' => $branch1->id]);
        $order1 = Order::factory()->create([
            'branch_id' => $branch1->id,
            'customer_id' => $customer1->id,
        ]);
        Payment::factory()->create([
            'order_id' => $order1->id,
            'amount' => 100000,
            'paid_at' => now(),
        ]);

        $customer2 = Customer::factory()->create(['branch_id' => $branch2->id]);
        $order2 = Order::factory()->create([
            'branch_id' => $branch2->id,
            'customer_id' => $customer2->id,
        ]);
        Payment::factory()->create([
            'order_id' => $order2->id,
            'amount' => 200000,
            'paid_at' => now(),
        ]);

        $result = $this->service->getRevenueByBranch();

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(2, count($result));

        $found = collect($result);
        $b1Revenue = $found->firstWhere('branch', $branch1->name);
        $b2Revenue = $found->firstWhere('branch', $branch2->name);

        $this->assertNotNull($b1Revenue);
        $this->assertNotNull($b2Revenue);
        $this->assertEquals(100000.0, $b1Revenue['revenue']);
        $this->assertEquals(200000.0, $b2Revenue['revenue']);
    }

    public function test_get_revenue_by_branch_only_includes_active_branches(): void
    {
        $activeBranch = Branch::factory()->create(['is_active' => true]);
        Branch::factory()->create(['is_active' => false]);

        $customer = Customer::factory()->create(['branch_id' => $activeBranch->id]);
        $order = Order::factory()->create([
            'branch_id' => $activeBranch->id,
            'customer_id' => $customer->id,
        ]);
        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 50000,
            'paid_at' => now(),
        ]);

        $result = $this->service->getRevenueByBranch();

        $activeResults = collect($result)->filter(fn($r) => $r['branch'] === $activeBranch->name);
        $this->assertGreaterThanOrEqual(1, $activeResults->count());
        $this->assertEquals(50000.0, $activeResults->first()['revenue']);

        $inactiveResults = collect($result)->filter(fn($r) => $r['revenue'] > 0 && !Branch::where('name', $r['branch'])->where('is_active', true)->exists());
        $this->assertCount(0, $inactiveResults);
    }

    public function test_get_payment_method_breakdown_returns_results(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $order1 = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
        ]);
        Payment::factory()->create([
            'order_id' => $order1->id,
            'amount' => 50000,
            'method' => 'cash',
            'paid_at' => now(),
        ]);

        $order2 = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
        ]);
        Payment::factory()->create([
            'order_id' => $order2->id,
            'amount' => 75000,
            'method' => 'transfer',
            'paid_at' => now(),
        ]);

        $result = $this->service->getPaymentMethodBreakdown();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        $totalAmount = array_sum(array_column($result, 'total'));
        $this->assertEquals(125000.0, $totalAmount);
    }

    public function test_get_payment_method_breakdown_filters_by_branch(): void
    {
        $otherBranch = Branch::factory()->create();

        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
        ]);
        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 50000,
            'method' => 'cash',
            'paid_at' => now(),
        ]);

        $customer2 = Customer::factory()->create(['branch_id' => $otherBranch->id]);
        $order2 = Order::factory()->create([
            'branch_id' => $otherBranch->id,
            'customer_id' => $customer2->id,
        ]);
        Payment::factory()->create([
            'order_id' => $order2->id,
            'amount' => 100000,
            'method' => 'transfer',
            'paid_at' => now(),
        ]);

        $result = $this->service->getPaymentMethodBreakdown($this->branch->id);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        $totalAmount = array_sum(array_column($result, 'total'));
        $this->assertEquals(50000.0, $totalAmount);
    }

    public function test_get_payment_method_breakdown_sums_amounts(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
        ]);
        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 50000,
            'method' => 'cash',
            'paid_at' => now(),
        ]);

        $order2 = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
        ]);
        Payment::factory()->create([
            'order_id' => $order2->id,
            'amount' => 30000,
            'method' => 'transfer',
            'paid_at' => now(),
        ]);

        $result = $this->service->getPaymentMethodBreakdown();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        $totalAmount = array_sum(array_column($result, 'total'));
        $this->assertEquals(80000.0, $totalAmount);
    }
}
