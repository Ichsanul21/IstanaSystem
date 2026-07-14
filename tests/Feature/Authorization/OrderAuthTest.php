<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServicePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAuthTest extends TestCase
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

    public function test_orders_index_requires_order_read(): void
    {
        $this->user->givePermissionTo('order.read');
        $response = $this->actingAs($this->user)->get(route('admin.orders.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('order.read');
        $this->actingAs($this->user)->get(route('admin.orders.index'))->assertForbidden();
    }

    public function test_orders_create_requires_order_create(): void
    {
        $this->user->givePermissionTo('order.create');
        $response = $this->actingAs($this->user)->get(route('admin.orders.create'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('order.create');
        $this->actingAs($this->user)->get(route('admin.orders.create'))->assertForbidden();
    }

    public function test_orders_store_requires_order_create(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $pricing = ServicePricing::factory()->create(['branch_id' => $this->branch->id]);

        $this->user->givePermissionTo('order.create');
        $response = $this->actingAs($this->user)->post(route('admin.orders.store'), [
            'customer_id' => $customer->id,
            'items' => [['service_pricing_id' => $pricing->id, 'quantity' => 2]],
            'notes' => 'Test order',
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('order.create');
        $this->actingAs($this->user)->post(route('admin.orders.store'), [
            'customer_id' => $customer->id,
            'items' => [['service_pricing_id' => $pricing->id, 'quantity' => 2]],
        ])->assertForbidden();
    }

    public function test_orders_show_requires_order_read(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->givePermissionTo('order.read');
        $response = $this->actingAs($this->user)->get(route('admin.orders.show', $order));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('order.read');
        $this->actingAs($this->user)->get(route('admin.orders.show', $order))->assertForbidden();
    }

    public function test_orders_edit_requires_order_update(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->givePermissionTo('order.update');
        $response = $this->actingAs($this->user)->get(route('admin.orders.edit', $order));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('order.update');
        $this->actingAs($this->user)->get(route('admin.orders.edit', $order))->assertForbidden();
    }

    public function test_orders_update_requires_order_update(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->givePermissionTo('order.update');
        $response = $this->actingAs($this->user)->put(route('admin.orders.update', $order), [
            'notes' => 'Updated notes',
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('order.update');
        $this->actingAs($this->user)->put(route('admin.orders.update', $order), [
            'notes' => 'Updated notes',
        ])->assertForbidden();
    }
}
