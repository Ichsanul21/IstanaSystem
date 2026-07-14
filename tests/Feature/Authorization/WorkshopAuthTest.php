<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductionStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopAuthTest extends TestCase
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

    public function test_index(): void
    {
        $this->user->givePermissionTo('workshop.read');

        $response = $this->actingAs($this->user)
            ->get(route('admin.workshop.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('workshop.read');

        $this->actingAs($this->user)
            ->get(route('admin.workshop.index'))
            ->assertForbidden();
    }

    public function test_scan(): void
    {
        $this->user->givePermissionTo('workshop.scan');

        $response = $this->actingAs($this->user)
            ->get(route('admin.workshop.scan'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('workshop.scan');

        $this->actingAs($this->user)
            ->get(route('admin.workshop.scan'))
            ->assertForbidden();
    }

    public function test_show(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $item = OrderItem::create(['order_id' => $order->id, 'service_id' => 1, 'quantity' => 1, 'price_per_unit' => 10000, 'subtotal' => 10000]);

        $this->user->givePermissionTo('workshop.read');

        $response = $this->actingAs($this->user)
            ->get(route('admin.workshop.items.show', $item));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('workshop.read');

        $this->actingAs($this->user)
            ->get(route('admin.workshop.items.show', $item))
            ->assertForbidden();
    }

    public function test_update_status(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $item = OrderItem::create(['order_id' => $order->id, 'service_id' => 1, 'quantity' => 1, 'price_per_unit' => 10000, 'subtotal' => 10000]);

        ProductionStatus::updateOrCreate(
            ['code' => 'TERIMA'],
            ['name' => 'Terima', 'sequence' => 1]
        );

        $this->user->givePermissionTo('workshop.update_status');

        $response = $this->actingAs($this->user)
            ->from(route('admin.workshop.items.show', $item))
            ->post(route('admin.workshop.update-status', $item), [
                'status' => 'TERIMA',
            ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('workshop.update_status');

        $freshItem = OrderItem::create(['order_id' => $order->id, 'service_id' => 1, 'quantity' => 1, 'price_per_unit' => 10000, 'subtotal' => 10000]);

        $this->actingAs($this->user)
            ->from(route('admin.workshop.items.show', $freshItem))
            ->post(route('admin.workshop.update-status', $freshItem), [
                'status' => 'TERIMA',
            ])
            ->assertForbidden();
    }
}
