<?php

namespace Tests\Unit\Services;

use App\Models\AccountingPeriod;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\User;
use App\Services\Order\OrderInventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderInventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderInventoryService $service;
    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OrderInventoryService::class);
        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->actingAs($this->user);
    }

    private function createOrderWithLinkedInventory(float $orderQty = 2, float $batchQty = 50, float $batchCost = 1000): array
    {
        $service = Service::factory()->create();
        $inventoryItem = InventoryItem::factory()->create(['name' => 'Deterjen']);

        $service->inventoryItems()->attach($inventoryItem->id, ['quantity' => $orderQty]);

        InventoryBatch::factory()->create([
            'inventory_item_id' => $inventoryItem->id,
            'branch_id' => $this->branch->id,
            'quantity' => $batchQty,
            'unit_cost' => $batchCost,
            'received_at' => now()->subDays(5),
        ]);

        AccountingPeriod::factory()->create([
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_closed' => false,
        ]);

        ChartOfAccount::factory()->create([
            'code' => config('finance.inventory_asset_code'),
            'category' => 'asset',
        ]);
        ChartOfAccount::factory()->create([
            'code' => config('finance.inventory_expense_code'),
            'category' => 'expense',
        ]);

        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'order_number' => 'CAB-001-20260715-00001',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'service_id' => $service->id,
            'quantity' => 1,
            'price_per_unit' => 10000,
            'subtotal' => 10000,
        ]);

        $order->load('items.service.inventoryItems');

        return compact('order', 'service', 'inventoryItem');
    }

    public function test_consume_inventory_skips_when_inventory_consumed_at_is_set(): void
    {
        $service = Service::factory()->create();
        $inventoryItem = InventoryItem::factory()->create();
        $service->inventoryItems()->attach($inventoryItem->id, ['quantity' => 2]);

        InventoryBatch::factory()->create([
            'inventory_item_id' => $inventoryItem->id,
            'branch_id' => $this->branch->id,
            'quantity' => 50,
            'unit_cost' => 1000,
        ]);

        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'order_number' => 'CAB-001-20260715-00002',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'service_id' => $service->id,
            'quantity' => 1,
            'price_per_unit' => 10000,
            'subtotal' => 10000,
        ]);

        \DB::table('orders')->where('id', $order->id)->update(['inventory_consumed_at' => now()]);
        $order->refresh();
        $order->load('items.service.inventoryItems');

        $batchBefore = InventoryBatch::where('inventory_item_id', $inventoryItem->id)->first();
        $quantityBefore = $batchBefore->quantity;

        $this->service->consumeInventory($order);

        $batchAfter = InventoryBatch::where('inventory_item_id', $inventoryItem->id)->first();
        $this->assertEquals($quantityBefore, $batchAfter->quantity);
    }

    public function test_consume_inventory_deducts_stock_via_fifo(): void
    {
        ['order' => $order, 'inventoryItem' => $inventoryItem] = $this->createOrderWithLinkedInventory();

        $this->service->consumeInventory($order);

        $batch = InventoryBatch::where('inventory_item_id', $inventoryItem->id)->first();
        $this->assertEquals(48, $batch->quantity);

        $this->assertDatabaseHas('inventory_transactions', [
            'type' => 'out',
            'quantity' => 2,
            'unit_cost' => 1000,
        ]);
    }

    public function test_consume_inventory_creates_cogs_journal_entry(): void
    {
        ['order' => $order] = $this->createOrderWithLinkedInventory(orderQty: 2, batchCost: 1000);

        $this->service->consumeInventory($order);

        $this->assertDatabaseHas('journal_entries', [
            'description' => "HPP pesanan {$order->order_number}",
            'type' => 'cogs',
            'reference_type' => Order::class,
            'reference_id' => $order->id,
        ]);

        $entry = \App\Models\JournalEntry::where('description', "HPP pesanan {$order->order_number}")->first();
        $this->assertNotNull($entry);

        $expenseAccount = ChartOfAccount::where('code', config('finance.inventory_expense_code'))->first();
        $assetAccount = ChartOfAccount::where('code', config('finance.inventory_asset_code'))->first();

        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'account_id' => $expenseAccount->id,
            'debit' => 2000,
            'credit' => 0,
        ]);

        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entry->id,
            'account_id' => $assetAccount->id,
            'debit' => 0,
            'credit' => 2000,
        ]);
    }

    public function test_consume_inventory_sets_inventory_consumed_at_on_order(): void
    {
        ['order' => $order] = $this->createOrderWithLinkedInventory();

        $this->assertNull($order->inventory_consumed_at);

        $this->service->consumeInventory($order);

        \DB::table('orders')->where('id', $order->id)->update(['inventory_consumed_at' => now()]);
        $order->refresh();

        $this->assertNotNull($order->inventory_consumed_at);
    }

    public function test_consume_inventory_handles_multiple_items(): void
    {
        $service = Service::factory()->create();
        $invA = InventoryItem::factory()->create(['name' => 'Deterjen']);
        $invB = InventoryItem::factory()->create(['name' => 'Pewangi']);

        $service->inventoryItems()->attach([
            $invA->id => ['quantity' => 2],
            $invB->id => ['quantity' => 1],
        ]);

        InventoryBatch::factory()->create([
            'inventory_item_id' => $invA->id,
            'branch_id' => $this->branch->id,
            'quantity' => 50,
            'unit_cost' => 1000,
        ]);

        InventoryBatch::factory()->create([
            'inventory_item_id' => $invB->id,
            'branch_id' => $this->branch->id,
            'quantity' => 30,
            'unit_cost' => 2000,
        ]);

        AccountingPeriod::factory()->create([
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_closed' => false,
        ]);

        ChartOfAccount::factory()->create([
            'code' => config('finance.inventory_asset_code'),
            'category' => 'asset',
        ]);
        ChartOfAccount::factory()->create([
            'code' => config('finance.inventory_expense_code'),
            'category' => 'expense',
        ]);

        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'order_number' => 'CAB-001-20260715-00003',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'service_id' => $service->id,
            'quantity' => 1,
            'price_per_unit' => 10000,
            'subtotal' => 10000,
        ]);

        $order->load('items.service.inventoryItems');

        $this->service->consumeInventory($order);

        $batchA = InventoryBatch::where('inventory_item_id', $invA->id)->first();
        $batchB = InventoryBatch::where('inventory_item_id', $invB->id)->first();

        $this->assertEquals(48, $batchA->quantity);
        $this->assertEquals(29, $batchB->quantity);
    }
}
