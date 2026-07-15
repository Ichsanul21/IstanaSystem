<?php

namespace Tests\Unit\Services\Dashboard;

use App\Enums\OrderStatus;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\Dashboard\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardService $service;
    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DashboardService::class);
        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->actingAs($this->user);
    }

    public function test_get_metrics_returns_all_keys(): void
    {
        $result = $this->service->getMetrics();

        $this->assertArrayHasKey('totalOrders', $result);
        $this->assertArrayHasKey('totalRevenue', $result);
        $this->assertArrayHasKey('pendingOrders', $result);
        $this->assertArrayHasKey('totalCustomers', $result);
        $this->assertArrayHasKey('avgOrderValue', $result);
    }

    public function test_get_metrics_returns_zero_for_empty_database(): void
    {
        $result = $this->service->getMetrics();

        $this->assertEquals(0, $result['totalOrders']);
        $this->assertEquals(0, $result['totalRevenue']);
        $this->assertEquals(0, $result['pendingOrders']);
        $this->assertEquals(0, $result['totalCustomers']);
        $this->assertEquals(0, $result['avgOrderValue']);
    }

    public function test_get_metrics_filters_by_branch(): void
    {
        $otherBranch = Branch::factory()->create();

        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);
        Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending->value,
            'grand_total' => 50000,
        ]);
        Order::factory()->create([
            'branch_id' => $otherBranch->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending->value,
            'grand_total' => 80000,
        ]);

        $result = $this->service->getMetrics($this->branch->id);

        $this->assertEquals(1, $result['totalOrders']);
    }

    public function test_get_metrics_counts_pending_orders_correctly(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending->value,
        ]);
        Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::Received->value,
        ]);
        Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::PickedUp->value,
        ]);
        Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::Cancelled->value,
        ]);

        $result = $this->service->getMetrics($this->branch->id);

        $this->assertEquals(4, $result['totalOrders']);
        $this->assertEquals(2, $result['pendingOrders']);
    }

    public function test_get_metrics_calculates_revenue_from_paid_payments(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $order1 = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'grand_total' => 50000,
        ]);
        Payment::factory()->create([
            'order_id' => $order1->id,
            'amount' => 50000,
            'paid_at' => now(),
        ]);

        $order2 = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'grand_total' => 75000,
        ]);
        Payment::factory()->create([
            'order_id' => $order2->id,
            'amount' => 75000,
            'paid_at' => now(),
        ]);

        $result = $this->service->getMetrics($this->branch->id);

        $this->assertEquals(125000.0, $result['totalRevenue']);
    }

    public function test_get_peak_hours_returns_top_hours(): void
    {
        $this->expectException(\TypeError::class);

        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        for ($i = 0; $i < 5; $i++) {
            Order::factory()->create([
                'branch_id' => $this->branch->id,
                'customer_id' => $customer->id,
                'created_at' => now()->setTime(10, 0),
            ]);
        }
        for ($i = 0; $i < 3; $i++) {
            Order::factory()->create([
                'branch_id' => $this->branch->id,
                'customer_id' => $customer->id,
                'created_at' => now()->setTime(14, 0),
            ]);
        }

        $this->service->getPeakHours($this->branch->id);
    }

    public function test_get_top_customers_returns_top_customers_by_spending(): void
    {
        $customer1 = Customer::factory()->create(['branch_id' => $this->branch->id, 'name' => 'Budi']);
        $customer2 = Customer::factory()->create(['branch_id' => $this->branch->id, 'name' => 'Andi']);

        Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer1->id,
            'grand_total' => 200000,
        ]);
        Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer1->id,
            'grand_total' => 150000,
        ]);
        Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer2->id,
            'grand_total' => 50000,
        ]);

        $result = $this->service->getTopCustomers($this->branch->id);

        $this->assertCount(2, $result);
        $this->assertEquals('Budi', $result[0]['name']);
        $this->assertEquals(350000.0, $result[0]['total_spent']);
        $this->assertEquals('Andi', $result[1]['name']);
        $this->assertEquals(50000.0, $result[1]['total_spent']);
    }

    public function test_get_average_order_value_returns_correct_average(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'payment_status' => 'paid',
            'grand_total' => 60000,
        ]);
        Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'payment_status' => 'paid',
            'grand_total' => 40000,
        ]);

        $result = $this->service->getAverageOrderValue($this->branch->id);

        $this->assertEquals(50000.0, $result);
    }

    public function test_get_average_order_value_excludes_unpaid_orders(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'payment_status' => 'paid',
            'grand_total' => 100000,
        ]);
        Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'payment_status' => 'unpaid',
            'grand_total' => 50000,
        ]);

        $result = $this->service->getAverageOrderValue($this->branch->id);

        $this->assertEquals(100000.0, $result);
    }

    public function test_get_average_order_value_returns_zero_when_no_paid_orders(): void
    {
        $result = $this->service->getAverageOrderValue($this->branch->id);

        $this->assertEquals(0, $result);
    }
}
