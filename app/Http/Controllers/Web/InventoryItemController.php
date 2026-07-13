<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Services\Inventory\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryItemController extends Controller
{
    public function __construct(protected FifoService $fifoService) {}

    public function index()
    {
        $items = InventoryItem::with(['batches' => fn($q) => $q->where('branch_id', currentBranchId())])
            ->latest()
            ->paginate(15);

        return view('inventory.items.index', compact('items'));
    }

    public function create()
    {
        return view('inventory.items.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|string|max:20',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data['code'] = 'INV-' . strtoupper(Str::random(6));

        InventoryItem::create($data);

        return redirect()->route('admin.inventory.index')->with('success', 'Item inventaris berhasil ditambahkan.');
    }

    public function show(InventoryItem $inventory)
    {
        $inventory->load(['batches' => fn($q) => $q->where('branch_id', currentBranchId())->with('transactions')]);
        $branches = Branch::where('is_active', true)->pluck('name', 'id');

        return view('inventory.items.show', ['item' => $inventory, 'branches' => $branches]);
    }

    public function edit(InventoryItem $inventory)
    {
        return view('inventory.items.edit', ['item' => $inventory]);
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|string|max:20',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $inventory->update($data);

        return redirect()->route('admin.inventory.index')->with('success', 'Item inventaris berhasil diperbarui.');
    }

    public function destroy(InventoryItem $inventory)
    {
        try {
            $inventory->delete();
            return redirect()->route('admin.inventory.index')->with('success', 'Item inventaris berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.inventory.index')->with('error', 'Item inventaris gagal dihapus.');
        }
    }

    public function addStock(InventoryItem $item, Request $request)
    {
        $data = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        $this->fifoService->addStock($item, currentBranchId(), $data['quantity'], $data['unit_cost']);

        return redirect()->route('admin.inventory.show', $item);
    }

    public function transfer(InventoryItem $item, Request $request)
    {
        $data = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
        ]);

        $deductions = $this->fifoService->deductStock($item, $data['from_branch_id'], $data['quantity'], 'Transfer to branch ' . $data['to_branch_id']);

        $totalCost = collect($deductions)->sum(fn($d) => $d['unit_cost'] * $d['quantity']);
        $totalQty = collect($deductions)->sum('quantity');
        $weightedAvgCost = $totalQty > 0 ? $totalCost / $totalQty : 0;

        $this->fifoService->addStock($item, $data['to_branch_id'], $data['quantity'], $weightedAvgCost, 'Transfer from branch ' . $data['from_branch_id']);

        return redirect()->route('admin.inventory.show', $item);
    }
}
