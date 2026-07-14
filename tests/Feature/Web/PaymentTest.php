<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
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

    public function test_create(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->get(route('admin.orders.payments.create', $order))
            ->assertOk();
    }

    public function test_store(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('admin.orders.payments.store', $order), [
                'amount' => 50000,
                'payment_method' => 'cash',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount' => 50000,
        ]);
    }

    public function test_store_validates_amount(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->post(route('admin.orders.payments.store', $order), [
                'amount' => 0,
                'payment_method' => 'cash',
            ])
            ->assertSessionHasErrors('amount');
    }

    public function test_show(): void
    {
        $payment = Payment::factory()->create();

        $this->actingAs($this->user)
            ->get(route('admin.orders.payments.show', ['order' => $payment->order_id, 'payment' => $payment]))
            ->assertOk();
    }
}
