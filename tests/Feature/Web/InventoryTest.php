<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
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
        InventoryItem::factory(3)->create();

        $this->actingAs($this->user)
            ->get(route('admin.inventory.index'))
            ->assertOk();
    }

    public function test_create(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.inventory.create'))
            ->assertOk();
    }

    public function test_store(): void
    {
        $this->actingAs($this->user)
            ->post(route('admin.inventory.store'), [
                'name' => 'Deterjen',
                'unit' => 'kg',
                'description' => 'Deterjen bubuk',
                'min_stock' => 5,
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.inventory.index'));

        $this->assertDatabaseHas('inventory_items', ['name' => 'Deterjen']);
    }

    public function test_show(): void
    {
        $item = InventoryItem::factory()->create();

        $this->actingAs($this->user)
            ->get(route('admin.inventory.show', $item))
            ->assertOk();
    }

    public function test_edit(): void
    {
        $item = InventoryItem::factory()->create();

        $this->actingAs($this->user)
            ->get(route('admin.inventory.edit', $item))
            ->assertOk();
    }

    public function test_update(): void
    {
        $item = InventoryItem::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->user)
            ->put(route('admin.inventory.update', $item), [
                'name' => 'New Name',
                'unit' => $item->unit,
                'description' => $item->description,
                'min_stock' => 5,
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.inventory.index'));

        $this->assertEquals('New Name', $item->fresh()->name);
    }

    public function test_destroy(): void
    {
        $item = InventoryItem::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('admin.inventory.destroy', $item))
            ->assertRedirect(route('admin.inventory.index'));

        $this->assertSoftDeleted($item);
    }

    public function test_add_stock(): void
    {
        $item = InventoryItem::factory()->create();

        $this->actingAs($this->user)
            ->post(route('admin.inventory.add-stock', $item), [
                'quantity' => 50,
                'unit_cost' => 5000,
            ])
            ->assertRedirect(route('admin.inventory.show', $item));

        $this->assertDatabaseHas('inventory_batches', [
            'inventory_item_id' => $item->id,
            'branch_id' => $this->branch->id,
        ]);
    }

    public function test_transfer(): void
    {
        $item = InventoryItem::factory()->create();
        $fromBranch = $this->branch;
        $toBranch = Branch::factory()->create();

        InventoryBatch::factory()->create([
            'inventory_item_id' => $item->id,
            'branch_id' => $fromBranch->id,
            'quantity' => 100,
            'unit_cost' => 5000,
        ]);

        $this->actingAs($this->user)
            ->post(route('admin.inventory.transfer', $item), [
                'quantity' => 30,
                'from_branch_id' => $fromBranch->id,
                'to_branch_id' => $toBranch->id,
            ])
            ->assertRedirect(route('admin.inventory.show', $item));
    }
}
