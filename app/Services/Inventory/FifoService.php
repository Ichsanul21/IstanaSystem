<?php

namespace App\Services\Inventory;

use App\Exceptions\InsufficientStockException;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;

class FifoService
{
    public function deductStock(InventoryItem $item, int $branchId, float $quantity, ?string $reference = null): array
    {
        $batches = InventoryBatch::where('inventory_item_id', $item->id)
            ->where('branch_id', $branchId)
            ->where('quantity', '>', 0)
            ->orderBy('received_at')
            ->orderBy('id')
            ->get();

        $remaining = $quantity;
        $deductions = [];

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $deductQty = min($remaining, $batch->quantity);

            $batch->decrement('quantity', $deductQty);

            InventoryTransaction::create([
                'inventory_batch_id' => $batch->id,
                'type' => 'out',
                'quantity' => $deductQty,
                'unit_cost' => $batch->unit_cost,
                'reference' => $reference,
                'created_by' => Auth::id(),
            ]);

            $deductions[] = [
                'batch_id' => $batch->id,
                'quantity' => $deductQty,
                'unit_cost' => $batch->unit_cost,
            ];

            $remaining -= $deductQty;
        }

        if ($remaining > 0) {
            throw new InsufficientStockException(
                "Insufficient stock for '{$item->name}': required {$quantity}, available only " . ($quantity - $remaining)
            );
        }

        return $deductions;
    }

    public function addStock(InventoryItem $item, int $branchId, float $quantity, float $unitCost, ?string $reference = null): InventoryBatch
    {
        $batch = InventoryBatch::create([
            'inventory_item_id' => $item->id,
            'branch_id' => $branchId,
            'batch_code' => 'BCH-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -4)),
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'received_at' => now(),
        ]);

        InventoryTransaction::create([
            'inventory_batch_id' => $batch->id,
            'type' => 'in',
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'reference' => $reference,
            'created_by' => Auth::id(),
        ]);

        return $batch;
    }

    public function getAvailableStock(InventoryItem $item, int $branchId): float
    {
        return InventoryBatch::where('inventory_item_id', $item->id)
            ->where('branch_id', $branchId)
            ->where('quantity', '>', 0)
            ->sum('quantity');
    }
}
