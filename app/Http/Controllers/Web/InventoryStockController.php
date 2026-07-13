<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Services\Inventory\FifoService;
use Illuminate\Http\Request;

class InventoryStockController extends Controller
{
    public function __construct(protected FifoService $fifoService) {}

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
