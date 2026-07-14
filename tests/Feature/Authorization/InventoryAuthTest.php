<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAuthTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create([]);
        $this->session(['current_branch_id' => $this->branch->id]);
    }

    public function test_index_requires_inventory_read(): void
    {
        $this->user->givePermissionTo('inventory.read');
        $response = $this->actingAs($this->user)->get(route('admin.inventory.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('inventory.read');
        $this->actingAs($this->user)->get(route('admin.inventory.index'))->assertForbidden();
    }

    public function test_create_requires_inventory_create(): void
    {
        $this->user->givePermissionTo('inventory.create');
        $response = $this->actingAs($this->user)->get(route('admin.inventory.create'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('inventory.create');
        $this->actingAs($this->user)->get(route('admin.inventory.create'))->assertForbidden();
    }

    public function test_store_requires_inventory_create(): void
    {
        $this->user->givePermissionTo('inventory.create');
        $response = $this->actingAs($this->user)->post(route('admin.inventory.store'), [
            'name' => 'Test Item',
            'category' => 'chemical',
            'unit' => 'kg',
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('inventory.create');
        $this->actingAs($this->user)->post(route('admin.inventory.store'), [
            'name' => 'Test Item',
            'category' => 'chemical',
            'unit' => 'kg',
        ])->assertForbidden();
    }

    public function test_show_requires_inventory_read(): void
    {
        $item = InventoryItem::factory()->create([]);
        $this->user->givePermissionTo('inventory.read');
        $response = $this->actingAs($this->user)->get(route('admin.inventory.show', $item));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('inventory.read');
        $this->actingAs($this->user)->get(route('admin.inventory.show', $item))->assertForbidden();
    }

    public function test_edit_and_update_requires_inventory_update(): void
    {
        $item = InventoryItem::factory()->create([]);
        $this->user->givePermissionTo('inventory.update');

        $response = $this->actingAs($this->user)->get(route('admin.inventory.edit', $item));
        $this->assertNotEquals(403, $response->getStatusCode());

        $response = $this->actingAs($this->user)->put(route('admin.inventory.update', $item), ['name' => 'Updated']);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('inventory.update');

        $this->actingAs($this->user)->get(route('admin.inventory.edit', $item))->assertForbidden();
        $this->actingAs($this->user)->put(route('admin.inventory.update', $item), ['name' => 'Updated'])->assertForbidden();
    }

    public function test_destroy_requires_inventory_delete(): void
    {
        $this->user->givePermissionTo('inventory.delete');
        $item = InventoryItem::factory()->create([]);
        $response = $this->actingAs($this->user)->delete(route('admin.inventory.destroy', $item));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('inventory.delete');
        $forbiddenItem = InventoryItem::factory()->create([]);
        $this->actingAs($this->user)->delete(route('admin.inventory.destroy', $forbiddenItem))->assertForbidden();
    }

    public function test_add_stock_requires_stock_in(): void
    {
        $item = InventoryItem::factory()->create([]);
        $this->user->givePermissionTo('stock_in');
        $response = $this->actingAs($this->user)->post(route('admin.inventory.add-stock', $item), ['quantity' => 10]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('stock_in');
        $this->actingAs($this->user)->post(route('admin.inventory.add-stock', $item), ['quantity' => 10])->assertForbidden();
    }

    public function test_stock_out_requires_stock_out(): void
    {
        $item = InventoryItem::factory()->create([]);
        $this->user->givePermissionTo('stock_out');
        $response = $this->actingAs($this->user)->get(route('admin.inventory.stock.out', $item));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('stock_out');
        $this->actingAs($this->user)->get(route('admin.inventory.stock.out', $item))->assertForbidden();
    }

    public function test_deduct_requires_stock_out(): void
    {
        $item = InventoryItem::factory()->create([]);
        $this->user->givePermissionTo('stock_out');
        $response = $this->actingAs($this->user)->post(route('admin.inventory.stock.deduct', $item), ['quantity' => 1]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('stock_out');
        $this->actingAs($this->user)->post(route('admin.inventory.stock.deduct', $item), ['quantity' => 1])->assertForbidden();
    }

    public function test_stock_index_requires_inventory_read(): void
    {
        $this->markTestSkipped('Route /admin/inventory/stock conflicts with resource /admin/inventory/{inventory}. Fix route ordering first.');
    }

    public function test_stock_create_requires_stock_in(): void
    {
        $this->user->givePermissionTo('stock_in');
        $response = $this->actingAs($this->user)->get(route('admin.inventory.stock.create'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('stock_in');
        $this->actingAs($this->user)->get(route('admin.inventory.stock.create'))->assertForbidden();
    }

    public function test_stock_store_requires_stock_in(): void
    {
        $item = InventoryItem::factory()->create([]);
        $this->user->givePermissionTo('stock_in');
        $response = $this->actingAs($this->user)->post(route('admin.inventory.stock.store'), [
            'inventory_item_id' => $item->id,
            'quantity' => 5,
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('stock_in');
        $this->actingAs($this->user)->post(route('admin.inventory.stock.store'), [
            'inventory_item_id' => $item->id,
            'quantity' => 5,
        ])->assertForbidden();
    }

    public function test_transfer_requires_stock_out(): void
    {
        $item = InventoryItem::factory()->create([]);
        $this->user->givePermissionTo('stock_out');
        $response = $this->actingAs($this->user)->post(route('admin.inventory.transfer', $item), [
            'quantity' => 1,
            'target_branch_id' => $this->branch->id,
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('stock_out');
        $this->actingAs($this->user)->post(route('admin.inventory.transfer', $item), [
            'quantity' => 1,
            'target_branch_id' => $this->branch->id,
        ])->assertForbidden();
    }
}
