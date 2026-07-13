<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function index()
    {
        $orders = Order::forCurrentBranch()
            ->with('customer', 'items')
            ->latest()
            ->paginate(15);

        return response()->json($orders);
    }

    public function show(Order $order)
    {
        $order->load(['items', 'payments', 'customer']);

        return response()->json($order);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.service_pricing_id' => 'required|exists:service_pricings,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $data['branch_id'] = currentBranchId();

        $order = app(\App\Services\Order\OrderService::class)->createOrder($data);

        return response()->json($order, 201);
    }

    public function updateStatus(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json($order);
    }

    public function payment(Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);

        $data = $request->validate([
            'method' => 'required|string|in:cash,transfer,qris,gateway',
            'amount' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:255',
        ]);

        $paymentService = app(\App\Services\Order\PaymentService::class);
        $payment = $paymentService->processPayment($order, $data);

        return response()->json([
            'success' => true,
            'data' => [
                'paid_amount' => (float) $payment->amount,
                'change_amount' => max(0, (float) $payment->amount - $order->grand_total),
                'payment_status' => $order->fresh()->payment_status ?? 'paid',
            ],
        ]);
    }

    public function refund(Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0|max:' . $order->grand_total,
            'reason' => 'required|string|max:500',
        ]);

        $refundService = app(\App\Services\Order\RefundService::class);
        $refund = $refundService->processRefund($order, $data);

        return response()->json([
            'success' => true,
            'data' => ['id' => $refund->id, 'status' => $refund->status],
        ], 201);
    }

    public function receipt($id)
    {
        $order = \App\Models\Order::with(['items.service', 'customer', 'branch'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer?->name ?? $order->customer_name,
                'items' => $order->items->map(fn($i) => [
                    'service' => $i->service?->name,
                    'quantity' => $i->quantity,
                    'price' => (float) $i->price_per_unit,
                    'subtotal' => (float) ($i->quantity * $i->price_per_unit),
                ]),
                'subtotal' => (float) $order->total_amount,
                'discount' => (float) ($order->discount_amount ?? 0),
                'tax' => (float) ($order->tax ?? 0),
                'total' => (float) $order->grand_total,
                'created_at' => $order->created_at,
                'branch' => $order->branch?->name ?? '',
            ],
        ]);
    }

    public function trackingStatus($id)
    {
        $order = \App\Models\Order::with(['items.orderItemStatusLogs', 'customer', 'branch'])->findOrFail($id);

        $items = $order->items->map(function ($item) {
            $latestLog = $item->orderItemStatusLogs()->latest()->first();
            return [
                'service_code' => $item->service?->code ?? '',
                'service_name' => $item->service?->name,
                'quantity' => $item->quantity,
                'current_status' => $latestLog?->to_status ?? 'received',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer?->name ?? $order->customer_name,
                'status' => $order->status,
                'items' => $items,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $data = $request->validate(['notes' => 'nullable|string|max:1000']);
        $order->update($data);

        return response()->json(['success' => true, 'data' => $order]);
    }

    public function destroy($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $order->delete();

        return response()->json(['success' => true, 'message' => 'Order deleted']);
    }
}
