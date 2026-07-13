<?php

namespace Tests\Unit\Services;

use App\Models\Branch;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\User;
use App\Services\Inventory\FifoService;
use App\Exceptions\InsufficientStockException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FifoServiceTest extends TestCase
{
    use RefreshDatabase;

    private FifoService $service;
    private InventoryItem $item;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FifoService::class);
        $this->item = InventoryItem::factory()->create();
        $this->branch = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->actingAs($user);
    }

    public function test_oldest_batch_consumed_first(): void
    {
        InventoryBatch::factory()->create([
            'inventory_item_id' => $this->item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 10,
            'unit_cost' => 1000,
            'received_at' => now()->subDays(10),
        ]);
        InventoryBatch::factory()->create([
            'inventory_item_id' => $this->item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 10,
            'unit_cost' => 2000,
            'received_at' => now()->subDays(5),
        ]);

        $deductions = $this->service->deductStock($this->item, $this->branch->id, 15);

        $this->assertCount(2, $deductions);
        $this->assertEquals(10, $deductions[0]['quantity']);
        $this->assertEquals(1000, $deductions[0]['unit_cost']);
        $this->assertEquals(5, $deductions[1]['quantity']);
        $this->assertEquals(2000, $deductions[1]['unit_cost']);
    }

    public function test_exact_quantity_from_single_batch(): void
    {
        InventoryBatch::factory()->create([
            'inventory_item_id' => $this->item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 10,
            'unit_cost' => 1000,
            'received_at' => now()->subDays(5),
        ]);

        $deductions = $this->service->deductStock($this->item, $this->branch->id, 10);

        $this->assertCount(1, $deductions);
        $this->assertEquals(10, $deductions[0]['quantity']);
    }

    public function test_insufficient_stock_throws_exception(): void
    {
        InventoryBatch::factory()->create([
            'inventory_item_id' => $this->item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 5,
            'unit_cost' => 1000,
        ]);

        $this->expectException(InsufficientStockException::class);

        $this->service->deductStock($this->item, $this->branch->id, 10);
    }

    public function test_add_stock_creates_batch_and_transaction(): void
    {
        $batch = $this->service->addStock($this->item, $this->branch->id, 50, 1500);

        $this->assertInstanceOf(InventoryBatch::class, $batch);
        $this->assertEquals(50, $batch->quantity);
        $this->assertEquals(1500, $batch->unit_cost);
        $this->assertSame($this->item->id, $batch->inventory_item_id);
        $this->assertSame($this->branch->id, $batch->branch_id);

        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_batch_id' => $batch->id,
            'type' => 'in',
            'quantity' => 50,
            'unit_cost' => 1500,
        ]);
    }

    public function test_get_available_stock_returns_sum(): void
    {
        InventoryBatch::factory()->create([
            'inventory_item_id' => $this->item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 10,
        ]);
        InventoryBatch::factory()->create([
            'inventory_item_id' => $this->item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 20,
        ]);

        $stock = $this->service->getAvailableStock($this->item, $this->branch->id);

        $this->assertSame(30.0, $stock);
    }

    public function test_deduct_reduces_batch_quantity(): void
    {
        $batch = InventoryBatch::factory()->create([
            'inventory_item_id' => $this->item->id,
            'branch_id' => $this->branch->id,
            'quantity' => 20,
            'unit_cost' => 1000,
        ]);

        $this->service->deductStock($this->item, $this->branch->id, 8);

        $batch->refresh();
        $this->assertEquals(12, $batch->quantity);
    }
}
