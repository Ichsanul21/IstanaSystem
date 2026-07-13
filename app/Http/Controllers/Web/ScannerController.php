<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index()
    {
        return view('workshop.scan');
    }

    public function lookup(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $orderItem = OrderItem::whereHas('order', function ($q) use ($request) {
            $q->where('qr_token', $request->token)
                ->orWhere('order_number', $request->token);
        })->with(['order.customer', 'productionStatuses' => fn($q) => $q->latest()])->first();

        if (!$orderItem) {
            return back()->with('error', 'Item dengan token tersebut tidak ditemukan.');
        }

        return redirect()->route('admin.workshop.items.show', $orderItem);
    }
}
