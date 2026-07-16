<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RefundRequest;
use App\Models\Order;
use App\Models\Refund;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index()
    {
        $refunds = Refund::with(['order', 'requester'])
            ->whereHas('order', fn($q) => $q->forCurrentBranch())
            ->latest()
            ->paginate(15);

        return view('refunds.index', compact('refunds'));
    }

    public function store(RefundRequest $request, Order $order)
    {
        $refund = Refund::create([
            'order_id' => $order->id,
            'amount' => $request->amount,
            'reason' => $request->reason,
            'status' => 'pending',
            'requested_by' => Auth::id(),
        ]);

        return redirect()->route('admin.refunds.index')->with('success', 'Refund berhasil diajukan.');
    }

    public function approve(Refund $refund)
    {
        $refund->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.refunds.index')->with('success', 'Refund dalam proses tindak lanjut.');
    }

    public function complete(Refund $refund)
    {
        $refund->update([
            'status' => 'approved',
        ]);

        $this->orderService->processRefund($refund->order, $refund);

        $refund->update([
            'status' => 'completed',
        ]);

        return redirect()->route('admin.refunds.index')->with('success', 'Refund berhasil diselesaikan.');
    }

    public function reject(Refund $refund)
    {
        $refund->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.refunds.index')->with('error', 'Refund berhasil ditolak.');
    }
}
