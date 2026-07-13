<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryBatch;
use App\Services\Inventory\InventoryService;
use App\Services\Inventory\FifoService;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected FifoService $fifoService
    ) {}

    public function itemsIndex(Request $request)
    {
        $query = InventoryItem::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return response()->json(['data' => $query->latest()->paginate($request->per_page ?? 15)]);
    }

    public function itemsShow($id)
    {
        $item = InventoryItem::with(['batches' => fn($q) => $q->where('branch_id', currentBranchId())])->findOrFail($id);

        return response()->json(['data' => $item]);
    }

    public function itemsStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|string|max:20',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data['code'] = 'INV-' . strtoupper(\Illuminate\Support\Str::random(6));
        $item = InventoryItem::create($data);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    public function itemsUpdate(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);

        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'unit' => 'nullable|string|max:20',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $item->update($data);

        return response()->json(['success' => true, 'message' => 'Item updated']);
    }

    public function stockIndex(Request $request)
    {
        $branchId = $request->branch_id ?? currentBranchId();

        $items = InventoryItem::with(['batches' => fn($q) => $q->where('branch_id', $branchId)])->get();

        return response()->json(['data' => $items->map(fn($item) => [
            'item_id' => $item->id,
            'item' => ['code' => $item->code, 'name' => $item->name, 'unit' => $item->unit],
            'branch_id' => $branchId,
            'total_quantity' => (float) $item->batches->sum('quantity'),
            'total_value' => (float) $item->batches->sum(fn($b) => $b->quantity * $b->unit_cost),
            'min_stock' => (float) ($item->min_stock ?? 0),
            'is_low' => $item->batches->sum('quantity') <= $item->min_stock,
        ])]);
    }

    public function stockDetail(Request $request, $itemId)
    {
        $item = InventoryItem::with(['batches' => fn($q) => $q->where('branch_id', currentBranchId())])->findOrFail($itemId);

        return response()->json(['data' => [
            'item' => ['name' => $item->name],
            'total_quantity' => (float) $item->batches->sum('quantity'),
            'total_value' => (float) $item->batches->sum(fn($b) => $b->quantity * $b->unit_cost),
            'batches' => $item->batches->map(fn($b) => [
                'batch_code' => $b->batch_code,
                'quantity' => (float) $b->quantity,
                'unit_cost' => (float) $b->unit_cost,
                'received_at' => $b->received_at?->toDateString(),
            ]),
        ]]);
    }

    public function stockIn(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'branch_id' => 'required|exists:branches,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
            'received_at' => 'nullable|date',
            'note' => 'nullable|string|max:500',
        ]);

        $item = InventoryItem::findOrFail($data['item_id']);
        $this->fifoService->addStock($item, $data['branch_id'], $data['quantity'], $data['unit_cost'], $data['note'] ?? null);

        return response()->json(['success' => true, 'message' => 'Stock added']);
    }

    public function stockOut(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'branch_id' => 'required|exists:branches,id',
            'quantity' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:500',
        ]);

        $item = InventoryItem::findOrFail($data['item_id']);
        $this->fifoService->deductStock($item, $data['branch_id'], $data['quantity'], $data['note'] ?? null);

        return response()->json(['success' => true, 'message' => 'Stock deducted']);
    }

    public function stockAdjust(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'branch_id' => 'required|exists:branches,id',
            'type' => 'required|string|in:plus,minus',
            'quantity' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:500',
        ]);

        $item = InventoryItem::findOrFail($data['item_id']);

        if ($data['type'] === 'plus') {
            $this->fifoService->addStock($item, $data['branch_id'], $data['quantity'], 0, $data['note'] ?? 'Adjustment +');
        } else {
            $this->fifoService->deductStock($item, $data['branch_id'], $data['quantity'], $data['note'] ?? 'Adjustment -');
        }

        return response()->json(['success' => true, 'message' => 'Stock adjusted']);
    }

    public function stockTransfer(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'quantity' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:500',
        ]);

        $item = InventoryItem::findOrFail($data['item_id']);
        $this->fifoService->deductStock($item, $data['from_branch_id'], $data['quantity'], 'Transfer to branch ' . $data['to_branch_id']);
        $this->fifoService->addStock($item, $data['to_branch_id'], $data['quantity'], 0, 'Transfer from branch ' . $data['from_branch_id']);

        return response()->json(['success' => true, 'message' => 'Stock transferred']);
    }

    public function alerts()
    {
        $branchId = currentBranchId();
        $items = $this->inventoryService->getLowStockItems($branchId);

        return response()->json(['data' => $items->map(fn($item) => [
            'id' => $item->id,
            'code' => $item->code,
            'name' => $item->name,
            'stock' => (float) $item->batches->sum('quantity'),
            'min_stock' => (float) ($item->min_stock ?? 0),
            'unit' => $item->unit,
        ])]);
    }
}
