<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Enums\ProductionStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::forCurrentBranch()
            ->with('customer:id,name', 'items');

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($paymentStatus = $request->payment_status) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($dateFrom = $request->date_from) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->date_to) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($customerId = $request->customer_id) {
            $query->where('customer_id', $customerId);
        }

        if ($branchId = $request->branch_id) {
            $query->where('branch_id', $branchId);
        }

        return ApiResponse::paginate($query->latest()->paginate($request->per_page ?? 15));
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

        return ApiResponse::success(['refund_id' => $refund->id, 'amount' => (float) $data['amount'], 'status' => $refund->status], null, 200);
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
            'pdf_url' => url("storage/receipts/{$order->order_number}.pdf"),
        ]);
    }

    public function trackingStatus($id)
    {
        $order = Order::with(['items.statusLogs.productionStatus', 'customer', 'branch'])->findOrFail($id);

        $allStatusLogs = $order->items->flatMap->statusLogs->sortBy('created_at');
        $productionStatuses = ProductionStatus::cases();

        $timeline = collect($productionStatuses)->map(fn($status) => [
            'code' => $status->value,
            'name' => $status->label(),
            'sequence' => $status->sequence(),
            'completed' => $allStatusLogs->contains(fn($log) => $log->productionStatus?->code === $status->value),
            'completed_at' => $allStatusLogs->where(fn($log) => $log->productionStatus?->code === $status->value)->first()?->created_at?->format('d/m/Y H:i'),
        ]);

        $currentStatus = $order->items
            ->flatMap->statusLogs
            ->sortByDesc('created_at')
            ->first()?->productionStatus?->value ?? 'TERIMA';

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
            'current_status' => $currentStatus,
            'payment_status' => $order->payment_status,
            'grand_total' => (float) $order->grand_total,
            'timeline' => $timeline,
            'items' => $items,
            'branch' => $order->branch ? ['id' => $order->branch->id, 'name' => $order->branch->name] : null,
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
