<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ServicePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
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

    public function test_index_returns_paginated_orders(): void
    {
        Order::factory(3)->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->getJson('/api/v1/orders')
            ->assertOk()
            ->assertJsonStructure(['success', 'data', 'meta' => ['current_page', 'last_page', 'per_page', 'total']]);
    }

    public function test_show_returns_order(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->getJson("/api/v1/orders/{$order->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $order->id);
    }

    public function test_store_creates_order(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $pricing = ServicePricing::factory()->create(['branch_id' => $this->branch->id]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/orders', [
                'customer_id' => $customer->id,
                'items' => [
                    ['service_pricing_id' => $pricing->id, 'quantity' => 2],
                ],
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.customer_id', $customer->id);
    }

    public function test_store_validates_items(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/v1/orders', [
                'customer_id' => null,
                'items' => [],
            ])
            ->assertStatus(422);
    }

    public function test_update_status(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/v1/orders/{$order->id}/status", [
                'status' => 'received',
            ])
            ->assertOk();

        $this->assertEquals('received', $order->fresh()->status);
    }

    public function test_unauthenticated_access_is_blocked(): void
    {
        $this->getJson('/api/v1/orders')
            ->assertUnauthorized();
    }
}
