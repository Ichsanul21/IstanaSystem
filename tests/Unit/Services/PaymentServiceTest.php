<?php

namespace Tests\Unit\Services;

use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\Order\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $service;
    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PaymentService::class);
        $this->branch = Branch::factory()->create(['code' => 'CAB-001']);
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->actingAs($this->user);

        ChartOfAccount::factory()->create(['code' => config('finance.kas_account_code'), 'category' => 'asset']);
        ChartOfAccount::factory()->create(['code' => config('finance.revenue_account_code'), 'category' => 'revenue']);
    }

    public function test_process_payment_creates_payment_record(): void
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
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount' => 50000,
        ]);
    }

    public function test_process_payment_sets_paid_when_amount_gte_grand_total(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'grand_total' => 50000,
        ]);

        $this->service->processPayment($order, [
            'amount' => 50000,
            'payment_method' => 'cash',
        ]);

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
    }

    public function test_process_payment_sets_paid_when_amount_exceeds_grand_total(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'grand_total' => 50000,
        ]);

        $this->service->processPayment($order, [
            'amount' => 60000,
            'payment_method' => 'cash',
        ]);

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
    }

    public function test_process_payment_sets_partial_when_amount_lt_grand_total(): void
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
    }

    public function test_process_payment_updates_order_status_to_received_when_fully_paid(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'grand_total' => 50000,
        ]);

        $this->service->processPayment($order, [
            'amount' => 50000,
            'payment_method' => 'cash',
        ]);

        $order->refresh();
        $this->assertSame('received', $order->status);
    }

    public function test_process_payment_does_not_update_status_when_partial(): void
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
        $this->assertSame('pending', $order->status);
    }
}
