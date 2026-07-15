<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ProductionStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class TrackingApiController extends Controller
{
    public function status($token)
    {
        $order = Order::with(['customer', 'branch', 'items.servicePricing.service', 'items.statusLogs.productionStatus'])
            ->where('qr_token', $token)
            ->first();

        if (!$order) {
            return ApiResponse::error('Order not found.', null, 404);
        }

        return ApiResponse::success($this->formatOrder($order));
    }

    public function verify(Request $request, $token)
    {
        $request->validate(['pin' => 'required|string']);

        $order = Order::with('customer')->where('qr_token', $token)->first();

        if (!$order) {
            return ApiResponse::error('Order not found.', null, 404);
        }

        if ($request->pin !== $order->customer->pin) {
            return ApiResponse::error('Invalid PIN.', null, 422);
        }

        return ApiResponse::success(['verified' => true]);
    }

    private function formatOrder(Order $order): array
    {
        $allStatusLogs = $order->items->flatMap->statusLogs->sortBy('created_at');

        $timeline = collect(ProductionStatus::cases())->map(fn($status) => [
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

        $items = $order->items->map(fn($item) => [
            'service_name' => $item->servicePricing?->service?->name ?? '-',
            'quantity' => (float) $item->quantity,
            'price' => (float) $item->price_per_unit,
        ]);

        return [
            'order_number' => $order->order_number,
            'customer_name' => $order->customer?->name ?? '-',
            'current_status' => $currentStatus,
            'payment_status' => $order->payment_status,
            'grand_total' => (float) $order->grand_total,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
            'estimated_finish' => $order->estimated_finish?->format('d/m/Y H:i'),
            'branch' => $order->branch ? ['id' => $order->branch->id, 'name' => $order->branch->name] : null,
            'timeline' => $timeline,
            'items' => $items,
        ];
    }
}
