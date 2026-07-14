<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentAuthTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        session(['current_branch_id' => $this->branch->id]);
    }

    public function test_create(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);

        $this->user->givePermissionTo('payment.create');

        $response = $this->actingAs($this->user)
            ->get(route('admin.orders.payments.create', $order));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('payment.create');

        $this->actingAs($this->user)
            ->get(route('admin.orders.payments.create', $order))
            ->assertForbidden();
    }

    public function test_store(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);

        $this->user->givePermissionTo('payment.create');

        $response = $this->actingAs($this->user)
            ->post(route('admin.orders.payments.store', $order), [
                'amount' => 50000,
                'payment_method' => 'cash',
            ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('payment.create');

        $this->actingAs($this->user)
            ->post(route('admin.orders.payments.store', $order), [
                'amount' => 50000,
                'payment_method' => 'cash',
            ])
            ->assertForbidden();
    }

    public function test_show(): void
    {
        $payment = Payment::factory()->create();

        $this->user->givePermissionTo('payment.read');

        $response = $this->actingAs($this->user)
            ->get(route('admin.orders.payments.show', ['order' => $payment->order_id, 'payment' => $payment]));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('payment.read');

        $this->actingAs($this->user)
            ->get(route('admin.orders.payments.show', ['order' => $payment->order_id, 'payment' => $payment]))
            ->assertForbidden();
    }
}
