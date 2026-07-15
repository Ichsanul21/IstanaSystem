<?php

namespace App\Services\Order;

use App\Models\ChartOfAccount;
use App\Models\Order;
use App\Services\FinanceService;
use App\Services\Inventory\FifoService;

class OrderInventoryService
{
    public function __construct(
        protected FifoService $fifoService,
        protected FinanceService $financeService
    ) {}

    public function consumeInventory(Order $order): void
    {
        if ($order->inventory_consumed_at) {
            return;
        }

        $branchId = $order->branch_id;
        $totalCost = 0;

        foreach ($order->items as $item) {
            $service = $item->service;

            if (!$service) {
                continue;
            }

            $inventoryItems = $service->inventoryItems;

            foreach ($inventoryItems as $invItem) {
                $qtyPerUnit = $invItem->pivot->quantity;
                $totalQty = $qtyPerUnit * $item->quantity;

                try {
                    $result = $this->fifoService->deductStock(
                        $invItem,
                        $branchId,
                        $totalQty,
                        "Order {$order->order_number}"
                    );

                    $cost = collect($result)->sum(fn($d) => $d['quantity'] * $d['unit_cost']);
                    $totalCost += $cost;
                } catch (\Exception $e) {
                    report($e);
                }
            }
        }

        if ($totalCost > 0) {
            $this->createCogsJournal($order, $totalCost);
        }

        $order->update(['inventory_consumed_at' => now()]);
    }

    protected function createCogsJournal(Order $order, float $totalCost): void
    {
        $assetAccount = ChartOfAccount::where('code', config('finance.inventory_asset_code'))->first();
        $expenseAccount = ChartOfAccount::where('code', config('finance.inventory_expense_code'))->first();

        if (!$assetAccount || !$expenseAccount) {
            return;
        }

        try {
            $this->financeService->createJournalEntry(
                "HPP pesanan {$order->order_number}",
                [
                    ['account_id' => $expenseAccount->id, 'debit' => $totalCost, 'credit' => 0, 'description' => 'Beban inventory'],
                    ['account_id' => $assetAccount->id, 'debit' => 0, 'credit' => $totalCost, 'description' => 'Persediaan terpakai'],
                ],
                $order->branch_id,
                'cogs',
                Order::class,
                $order->id
            );
        } catch (\Exception $e) {
            report($e);
        }
    }
}
