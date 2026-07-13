<?php

namespace Tests\Unit\Services;

use App\Models\Branch;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\User;
use App\Services\Inventory\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private InventoryService $service;
    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(InventoryService::class);
        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->actingAs($this->user);
    }

    public function test_get_low_stock_items_returns_items_below_min_stock(): void
    {
        $item = InventoryItem::factory()->create(['min_stock' => 10, 'is_active' => true]);
        InventoryBatch::factory()->create([
            'inventory_item_id' => $item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 3,
        ]);

        $lowStock = $this->service->getLowStockItems($this->branch->id);

        $this->assertCount(1, $lowStock);
        $this->assertEquals($item->id, $lowStock->first()->id);
    }

    public function test_get_low_stock_items_excludes_items_with_sufficient_stock(): void
    {
        $item = InventoryItem::factory()->create(['min_stock' => 10, 'is_active' => true]);
        InventoryBatch::factory()->create([
            'inventory_item_id' => $item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 50,
        ]);

        $lowStock = $this->service->getLowStockItems($this->branch->id);

        $this->assertCount(0, $lowStock);
    }

    public function test_get_low_stock_items_excludes_items_with_no_min_stock(): void
    {
        $item = InventoryItem::factory()->create(['min_stock' => 0, 'is_active' => true]);
        InventoryBatch::factory()->create([
            'inventory_item_id' => $item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 3,
        ]);

        $lowStock = $this->service->getLowStockItems($this->branch->id);

        $this->assertCount(0, $lowStock);
    }

    public function test_get_low_stock_items_excludes_inactive_items(): void
    {
        $item = InventoryItem::factory()->create(['min_stock' => 10, 'is_active' => false]);
        InventoryBatch::factory()->create([
            'inventory_item_id' => $item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 3,
        ]);

        $lowStock = $this->service->getLowStockItems($this->branch->id);

        $this->assertCount(0, $lowStock);
    }

    public function test_get_stock_value_returns_sum_of_batch_values(): void
    {
        $item = InventoryItem::factory()->create();
        InventoryBatch::factory()->create([
            'inventory_item_id' => $item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 10,
            'unit_cost' => 5000,
        ]);
        InventoryBatch::factory()->create([
            'inventory_item_id' => $item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 5,
            'unit_cost' => 3000,
        ]);

        $value = $this->service->getStockValue($this->branch->id);

        $this->assertSame(65000.0, $value);
    }

    public function test_get_stock_value_returns_zero_when_no_batches(): void
    {
        $value = $this->service->getStockValue($this->branch->id);

        $this->assertSame(0.0, $value);
    }

    public function test_get_stock_value_excludes_other_branches(): void
    {
        $otherBranch = Branch::factory()->create();
        $item = InventoryItem::factory()->create();
        InventoryBatch::factory()->create([
            'inventory_item_id' => $item->id,
            'branch_id' => $otherBranch->id,
            'quantity' => 10,
            'unit_cost' => 5000,
        ]);

        $value = $this->service->getStockValue($this->branch->id);

        $this->assertSame(0.0, $value);
    }

    public function test_transfer_stock_deducts_from_source(): void
    {
        $otherBranch = Branch::factory()->create();
        $item = InventoryItem::factory()->create();
        InventoryBatch::factory()->create([
            'inventory_item_id' => $item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 20,
            'unit_cost' => 5000,
        ]);

        $this->service->transferStock($item, $this->branch->id, $otherBranch->id, 8);

        $sourceStock = InventoryBatch::where('inventory_item_id', $item->id)
            ->where('branch_id', $this->branch->id)
            ->sum('quantity');

        $this->assertEquals(12, $sourceStock);
    }

    public function test_transfer_stock_adds_to_destination(): void
    {
        $otherBranch = Branch::factory()->create();
        $item = InventoryItem::factory()->create();
        InventoryBatch::factory()->create([
            'inventory_item_id' => $item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 20,
            'unit_cost' => 5000,
        ]);

        $this->service->transferStock($item, $this->branch->id, $otherBranch->id, 8);

        $destStock = InventoryBatch::where('inventory_item_id', $item->id)
            ->where('branch_id', $otherBranch->id)
            ->sum('quantity');

        $this->assertEquals(8, $destStock);
    }

    public function test_adjust_stock_adds_stock(): void
    {
        $item = InventoryItem::factory()->create();

        $this->service->adjustStock($item, $this->branch->id, 15, 2000, 'Add stock');

        $stock = InventoryBatch::where('inventory_item_id', $item->id)
            ->where('branch_id', $this->branch->id)
            ->sum('quantity');

        $this->assertEquals(15, $stock);
        $this->assertDatabaseHas('inventory_transactions', [
            'type' => 'in',
            'quantity' => 15,
            'reference' => 'Adjustment: Add stock',
        ]);
    }

    public function test_adjust_stock_deducts_stock(): void
    {
        $item = InventoryItem::factory()->create();
        InventoryBatch::factory()->create([
            'inventory_item_id' => $item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 20,
            'unit_cost' => 1000,
        ]);

        $this->service->adjustStock($item, $this->branch->id, -8, 0, 'Remove damaged');

        $stock = InventoryBatch::where('inventory_item_id', $item->id)
            ->where('branch_id', $this->branch->id)
            ->sum('quantity');

        $this->assertEquals(12, $stock);
        $this->assertDatabaseHas('inventory_transactions', [
            'type' => 'out',
            'quantity' => 8,
            'reference' => 'Adjustment: Remove damaged',
        ]);
    }
}
