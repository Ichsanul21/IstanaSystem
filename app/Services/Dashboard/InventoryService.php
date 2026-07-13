<?php

namespace App\Services\Dashboard;

use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;

class InventoryService
{
    public function getStockValue(?int $branchId = null): float
    {
        return InventoryBatch::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get()
            ->sum(fn($b) => $b->quantity * $b->unit_cost);
    }

    public function getLowStockItems(?int $branchId = null): array
    {
        return InventoryItem::where('is_active', true)
            ->get()
            ->filter(function ($item) use ($branchId) {
                $totalQty = $item->batches()
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->sum('quantity');
                return $totalQty <= $item->min_stock;
            })
            ->map(function ($item) use ($branchId) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code,
                    'stock' => (float) $item->batches()
                        ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                        ->sum('quantity'),
                    'min_stock' => (float) $item->min_stock,
                    'unit' => $item->unit,
                ];
            })
            ->values()
            ->toArray();
    }

    public function getStockMovement(?int $branchId = null): array
    {
        return InventoryTransaction::with(['batch.item', 'batch.branch'])
            ->whereHas('batch', fn($q) => $q->when($branchId, fn($qq) => $qq->where('branch_id', $branchId)))
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($t) => [
                'item' => $t->batch?->item?->name ?? '-',
                'type' => $t->type,
                'quantity' => (float) $t->quantity,
                'reference' => $t->reference,
                'created_at' => $t->created_at->format('Y-m-d H:i'),
            ])
            ->toArray();
    }
}
