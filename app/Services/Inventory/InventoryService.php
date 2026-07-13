<?php

namespace App\Services\Inventory;

use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InventoryService
{
    public function __construct(protected FifoService $fifoService) {}

    public function getLowStockItems(int $branchId): Collection
    {
        return InventoryItem::where('is_active', true)
            ->where('min_stock', '>', 0)
            ->get()
            ->filter(function ($item) use ($branchId) {
                $stock = $this->fifoService->getAvailableStock($item, $branchId);
                return $stock <= $item->min_stock;
            })
            ->values();
    }

    public function getStockValue(int $branchId): float
    {
        return InventoryBatch::where('branch_id', $branchId)
            ->where('quantity', '>', 0)
            ->get()
            ->sum(fn($b) => $b->quantity * $b->unit_cost);
    }

    public function transferStock(InventoryItem $item, int $fromBranchId, int $toBranchId, float $quantity, ?string $notes = null): void
    {
        $reference = 'Transfer-' . $fromBranchId . '-' . $toBranchId . '-' . Str::random(6);

        $this->fifoService->deductStock($item, $fromBranchId, $quantity, $reference);
        $this->fifoService->addStock($item, $toBranchId, $quantity, 0, $reference);
    }

    public function adjustStock(InventoryItem $item, int $branchId, float $quantity, float $unitCost = 0, ?string $reason = null): void
    {
        if ($quantity > 0) {
            $this->fifoService->addStock($item, $branchId, $quantity, $unitCost, 'Adjustment: ' . $reason);
        } elseif ($quantity < 0) {
            $this->fifoService->deductStock($item, $branchId, abs($quantity), 'Adjustment: ' . $reason);
        }
    }
}
