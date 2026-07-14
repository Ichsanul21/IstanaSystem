<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function index()
    {
        $orders = Order::forCurrentBranch()
            ->with('customer:id,name', 'items')
            ->latest()
            ->paginate(15);

        return ApiResponse::paginate($orders);
    }

    public function show(Order $order)
    {
        $order->load(['items.servicePricing.service', 'payments', 'customer']);

        return ApiResponse::success($order);
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

        return ApiResponse::success($order, null, 201);
    }

    public function updateStatus(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:' . implode(',', array_map(fn($c) => $c->value, OrderStatus::cases())),
        ]);

        $order->update(['status' => $request->status]);

        return ApiResponse::success($order);
    }

    public function payment(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'method' => 'required|string|in:cash,transfer,qris,gateway',
            'amount' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:255',
        ]);

        $paymentService = app(\App\Services\Order\PaymentService::class);
        $payment = $paymentService->processPayment($order, $data);

        return ApiResponse::success([
            'paid_amount' => (float) $payment->amount,
            'change_amount' => max(0, (float) $payment->amount - $order->grand_total),
            'payment_status' => $order->fresh()->payment_status ?? 'paid',
        ]);
    }

    public function refund(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0|max:' . $order->grand_total,
            'reason' => 'required|string|max:500',
        ]);

        $refundService = app(\App\Services\Order\RefundService::class);
        $refund = $refundService->processRefund($order, $data);

        return ApiResponse::success(['id' => $refund->id, 'status' => $refund->status], null, 201);
    }

    public function receipt($id)
    {
        $order = Order::with(['items.servicePricing.service', 'customer', 'branch'])->findOrFail($id);

        return ApiResponse::success([
            'order_number' => $order->order_number,
            'customer_name' => $order->customer?->name ?? $order->customer_name,
            'items' => $order->items->map(fn($i) => [
                'service' => $i->servicePricing?->service?->name,
                'quantity' => (float) $i->quantity,
                'price' => (float) $i->price_per_unit,
                'subtotal' => (float) ($i->quantity * $i->price_per_unit),
            ]),
            'subtotal' => (float) $order->total_amount,
            'discount' => (float) ($order->discount_amount ?? 0),
            'tax' => (float) ($order->tax ?? 0),
            'total' => (float) $order->grand_total,
            'created_at' => $order->created_at,
            'branch' => $order->branch?->name ?? '',
        ]);
    }

    public function trackingStatus($id)
    {
        $order = Order::with(['items.statusLogs.productionStatus', 'customer', 'branch'])->findOrFail($id);

        $items = $order->items->map(function ($item) {
            $latestLog = $item->statusLogs->first();
            return [
                'service_code' => $item->servicePricing?->service?->code ?? '',
                'service_name' => $item->servicePricing?->service?->name,
                'quantity' => (float) $item->quantity,
                'current_status' => $latestLog?->productionStatus?->code ?? 'TERIMA',
            ];
        });

        return ApiResponse::success([
            'order_number' => $order->order_number,
            'customer_name' => $order->customer?->name ?? $order->customer_name,
            'status' => $order->status,
            'items' => $items,
        ]);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate(['notes' => 'nullable|string|max:1000']);
        $order->update($data);

        return ApiResponse::success($order);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return ApiResponse::success(null, 'Order deleted');
    }
}
