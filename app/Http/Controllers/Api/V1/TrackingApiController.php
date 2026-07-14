<?php

namespace App\Http\Controllers\Api\V1;

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
        $statuses = ['received', 'washed', 'dried', 'ironed', 'packed', 'ready_for_pickup', 'picked_up'];
        $allStatusLogs = $order->items->flatMap->statusLogs->sortBy('created_at');

        $timeline = collect($statuses)->map(fn($code) => [
            'status' => $code,
            'completed' => $allStatusLogs->contains(fn($log) => $log->productionStatus?->code === $code),
            'completed_at' => $allStatusLogs->where(fn($log) => $log->productionStatus?->code === $code)->first()?->created_at?->format('d/m/Y H:i'),
        ]);

        $currentStep = $timeline->search(fn($s) => !$s['completed']);
        $currentStep = $currentStep === false ? count($statuses) : $currentStep;

        $items = $order->items->map(fn($item) => [
            'service_name' => $item->servicePricing?->service?->name ?? '-',
            'quantity' => (float) $item->quantity,
            'price' => (float) $item->price_per_unit,
        ]);

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer?->name ?? '-',
            'qr_token' => $order->qr_token,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
            'status' => $order->status,
            'branch' => $order->branch?->name,
            'timeline' => $timeline,
            'items' => $items,
            'current_step' => $currentStep,
            'total_steps' => count($statuses),
            'estimated_finish' => $order->estimated_finish?->format('d/m/Y H:i'),
        ];
    }
}
