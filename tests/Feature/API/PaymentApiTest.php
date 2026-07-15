<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ServicePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentApiTest extends TestCase
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

    public function test_snap_token_returns_token_for_valid_order(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/payments/midtrans/snap', [
                'order_id' => $order->id,
            ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['snap_token', 'snap_redirect_url'],
            ]);
    }

    public function test_snap_token_validates_order_exists(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/v1/payments/midtrans/snap', [
                'order_id' => 99999,
            ])
            ->assertStatus(422);
    }

    public function test_status_returns_payment_status(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->getJson("/api/v1/payments/{$order->id}/status")
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['transaction_id', 'status', 'payment_type', 'paid_at'],
            ]);
    }

    public function test_status_returns_404_for_nonexistent_order(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/payments/99999/status')
            ->assertStatus(404);
    }

    public function test_unauthenticated_access_is_blocked(): void
    {
        $order = Order::factory()->create();

        $this->getJson("/api/v1/payments/{$order->id}/status")
            ->assertUnauthorized();
    }
}
