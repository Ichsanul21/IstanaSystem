<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ServicePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopTest extends TestCase
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
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        OrderItem::create(['order_id' => $order->id, 'service_id' => 1, 'quantity' => 1, 'price_per_unit' => 10000, 'subtotal' => 10000]);
        OrderItem::create(['order_id' => $order->id, 'service_id' => 1, 'quantity' => 2, 'price_per_unit' => 15000, 'subtotal' => 30000]);

        $this->actingAs($this->user)
            ->get(route('admin.workshop.index'))
            ->assertOk();
    }

    public function test_scan(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.workshop.scan'))
            ->assertOk();
    }

    public function test_show(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $item = OrderItem::create(['order_id' => $order->id, 'service_id' => 1, 'quantity' => 1, 'price_per_unit' => 10000, 'subtotal' => 10000]);

        $this->actingAs($this->user)
            ->get(route('admin.workshop.items.show', $item))
            ->assertOk();
    }

    public function test_update_status(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $item = OrderItem::create(['order_id' => $order->id, 'service_id' => 1, 'quantity' => 1, 'price_per_unit' => 10000, 'subtotal' => 10000]);

        \App\Models\ProductionStatus::updateOrCreate(
            ['code' => 'TERIMA'],
            ['name' => 'Terima', 'sequence' => 1]
        );

        $this->actingAs($this->user)
            ->from(route('admin.workshop.items.show', $item))
            ->post(route('admin.workshop.update-status', $item), [
                'status' => 'TERIMA',
            ])
            ->assertRedirect(route('admin.workshop.items.show', $item));

        $this->assertDatabaseHas('order_item_status_logs', [
            'order_item_id' => $item->id,
        ]);
    }
}
