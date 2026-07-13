<?php

namespace Tests\Unit\Services;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Service;
use App\Models\ServicePricing;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $service;
    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OrderService::class);
        $this->branch = Branch::factory()->create(['code' => 'CAB-001']);
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->actingAs($this->user);
    }

    public function test_create_order_generates_order_number(): void
    {
        $service = Service::where('code', 'CK')->first() ?? Service::factory()->create(['code' => 'CK']);
        $pricing = ServicePricing::factory()->create([
            'service_id' => $service->id,
            'branch_id' => $this->branch->id,
            'price' => 5000,
        ]);

        $order = $this->service->createOrder([
            'branch_id' => $this->branch->id,
            'notes' => 'Test order',
            'items' => [
                ['service_pricing_id' => $pricing->id, 'quantity' => 3],
            ],
        ]);

        $this->assertMatchesRegularExpression('/^CAB-001-\d{8}-\d{5}$/', $order->order_number);
        $this->assertSame('pending', $order->status);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertEquals(15000, $order->total_amount);
    }

    public function test_process_payment_updates_order_status(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'grand_total' => 50000,
        ]);

        $payment = $this->service->processPayment($order, [
            'amount' => 50000,
            'payment_method' => 'cash',
        ]);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals(50000, $payment->amount);
        $this->assertSame('cash', $payment->method);

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('processing', $order->status);
    }

    public function test_process_payment_with_partial_amount(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'grand_total' => 100000,
        ]);

        $this->service->processPayment($order, [
            'amount' => 30000,
            'payment_method' => 'cash',
        ]);

        $order->refresh();
        $this->assertSame('partial', $order->payment_status);
        $this->assertSame('pending', $order->status);
    }

    public function test_process_refund_updates_payment_status(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
        ]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 50000,
        ]);
        $refund = Refund::factory()->create([
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'amount' => 50000,
            'status' => 'requested',
        ]);

        $this->service->processRefund($order, $refund);

        $refund->refresh();
        $this->assertSame('approved', $refund->status);

        $order->refresh();
        $this->assertSame('refunded', $order->payment_status);
    }
}
