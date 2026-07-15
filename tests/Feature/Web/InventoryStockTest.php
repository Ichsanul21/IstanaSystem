<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryStockTest extends TestCase
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
        $item = InventoryItem::factory()->create();
        InventoryBatch::factory()->create([
            'inventory_item_id' => $item->id,
            'branch_id' => $this->branch->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('admin.inventory.stock.index'))
            ->assertOk();
    }

    public function test_create(): void
    {
        InventoryItem::factory(3)->create();

        $this->actingAs($this->user)
            ->get(route('admin.inventory.stock.create'))
            ->assertOk();
    }

    public function test_store(): void
    {
        $item = InventoryItem::factory()->create();

        $this->actingAs($this->user)
            ->post(route('admin.inventory.stock.store'), [
                'inventory_item_id' => $item->id,
                'quantity' => 50,
                'unit_price' => 5000,
                'batch_number' => 'BCH-001',
                'notes' => 'Stok awal',
            ])
            ->assertRedirect(route('admin.inventory.stock.index'));

        $this->assertDatabaseHas('inventory_stocks', [
            'inventory_item_id' => $item->id,
            'branch_id' => $this->branch->id,
        ]);
    }
}
