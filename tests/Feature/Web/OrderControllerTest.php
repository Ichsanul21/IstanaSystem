<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServicePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
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

    public function test_index(): void
    {
        Order::factory(3)->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->get(route('admin.orders.index'))
            ->assertOk();
    }

    public function test_index_filters_by_status(): void
    {
        Order::factory()->create(['branch_id' => $this->branch->id, 'status' => 'pending']);
        Order::factory()->create(['branch_id' => $this->branch->id, 'status' => 'completed']);

        $this->actingAs($this->user)
            ->get(route('admin.orders.index', ['status' => 'pending']))
            ->assertOk();
    }

    public function test_create(): void
    {
        ServicePricing::factory()->create([
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->user)
            ->get(route('admin.orders.create'))
            ->assertOk();
    }

    public function test_store(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $pricing = ServicePricing::factory()->create([
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->user)
            ->post(route('admin.orders.store'), [
                'customer_id' => $customer->id,
                'items' => [
                    ['service_pricing_id' => $pricing->id, 'quantity' => 2],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['customer_id' => $customer->id]);
    }

    public function test_show(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->get(route('admin.orders.show', $order))
            ->assertOk();
    }

    public function test_edit(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->get(route('admin.orders.edit', $order))
            ->assertOk();
    }

    public function test_update(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->user)
            ->from(route('admin.orders.edit', $order))
            ->put(route('admin.orders.update', $order), [
                'status' => 'received',
            ])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertEquals('received', $order->fresh()->status);
    }

    public function test_print(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->get(route('admin.orders.receipt', $order))
            ->assertOk();
    }

    public function test_destroy(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->user)
            ->delete(route('admin.orders.destroy', $order))
            ->assertRedirect(route('admin.orders.index'));

        $this->assertSoftDeleted($order);
    }

    public function test_destroy_fails_for_completed_order(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'status' => 'completed',
        ]);

        $this->actingAs($this->user)
            ->delete(route('admin.orders.destroy', $order))
            ->assertSessionHasErrors();
    }
}
