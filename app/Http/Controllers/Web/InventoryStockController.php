<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryStock;
use App\Services\Inventory\FifoService;
use Illuminate\Http\Request;

class InventoryStockController extends Controller
{
    public function __construct(protected FifoService $fifoService) {}

    public function index()
    {
        $stocks = InventoryStock::query()
            ->with('item')
            ->forCurrentBranch()
            ->latest()
            ->paginate(25);

        return view('inventory.stock.index', compact('stocks'));
    }

    public function create()
    {
        $items = InventoryItem::all();

        return view('inventory.stock.create', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'nullable|numeric|min:0',
            'batch_number' => 'nullable|string|max:255',
            'expired_at' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['branch_id'] = currentBranchId();
        InventoryStock::create($data);

        return redirect()->route('admin.inventory.stock.index')
            ->with('success', 'Stok berhasil ditambahkan.');
    }

    public function out(InventoryItem $item)
    {
        $item->load(['batches' => fn($q) => $q->where('branch_id', currentBranchId())->where('quantity', '>', 0)]);

        return view('inventory.stock.out', compact('item'));
    }

    public function deduct(Request $request, InventoryItem $item)
    {
        $data = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:255',
        ]);

        try {
            $this->fifoService->deductStock($item, currentBranchId(), $data['quantity'], $data['reference']);

            return redirect()->route('admin.inventory.show', $item)->with('success', 'Stok berhasil dikurangi.');
        } catch (\App\Exceptions\InsufficientStockException $e) {
            return redirect()->route('admin.inventory.show', $item)->with('error', $e->getMessage());
        }
    }
}
