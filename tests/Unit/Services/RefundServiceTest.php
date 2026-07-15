<?php

namespace Tests\Unit\Services;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use App\Services\Order\RefundService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefundServiceTest extends TestCase
{
    use RefreshDatabase;

    private RefundService $service;
    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RefundService::class);
        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->actingAs($this->user);
    }

    public function test_process_refund_creates_refund_with_requested_status(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $payment = Payment::factory()->create(['order_id' => $order->id, 'amount' => 50000]);

        $refund = $this->service->processRefund($order, [
            'payment_id' => $payment->id,
            'amount' => 25000,
            'reason' => 'Customer not satisfied',
        ]);

        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertSame('requested', $refund->status);
        $this->assertEquals(25000, $refund->amount);
        $this->assertSame($order->id, $refund->order_id);
        $this->assertSame($payment->id, $refund->payment_id);
        $this->assertDatabaseHas('refunds', [
            'order_id' => $order->id,
            'status' => 'requested',
        ]);
    }

    public function test_approve_sets_status_approved_and_reduces_order_total(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'grand_total' => 100000,
        ]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 100000,
        ]);
        $refund = Refund::factory()->create([
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'amount' => 30000,
            'status' => 'requested',
        ]);

        $result = $this->service->approve($refund);

        $this->assertSame('approved', $result->status);
        $this->assertNotNull($result->approved_at);
        $this->assertSame($this->user->id, $result->approved_by);

        $order->refresh();
        $this->assertEquals(70000, (float) $order->grand_total);
    }

    public function test_approve_sets_payment_status_refunded_when_fully_refunded(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'grand_total' => 50000,
            'payment_status' => 'paid',
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

        $this->service->approve($refund);

        $order->refresh();
        $this->assertSame('refunded', $order->payment_status);
    }

    public function test_approve_sets_partial_refund_when_partially_refunded(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'grand_total' => 100000,
            'payment_status' => 'paid',
        ]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 100000,
        ]);
        $refund = Refund::factory()->create([
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'amount' => 30000,
            'status' => 'requested',
        ]);

        $this->service->approve($refund);

        $order->refresh();
        $this->assertSame('partial_refund', $order->payment_status);
    }

    public function test_reject_sets_status_rejected(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $refund = Refund::factory()->create([
            'order_id' => $order->id,
            'status' => 'requested',
        ]);

        $result = $this->service->reject($refund, 'Insufficient documentation');

        $this->assertSame('rejected', $result->status);
        $this->assertSame('Insufficient documentation', $result->reason);
        $this->assertDatabaseHas('refunds', [
            'id' => $refund->id,
            'status' => 'rejected',
        ]);
    }

    public function test_complete_sets_status_completed(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $refund = Refund::factory()->create([
            'order_id' => $order->id,
            'status' => 'approved',
        ]);

        $result = $this->service->complete($refund);

        $this->assertSame('completed', $result->status);
        $this->assertDatabaseHas('refunds', [
            'id' => $refund->id,
            'status' => 'completed',
        ]);
    }
}
