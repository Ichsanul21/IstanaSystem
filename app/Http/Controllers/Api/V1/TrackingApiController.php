<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class TrackingApiController extends Controller
{
    public function status($token)
    {
        $order = Order::with('customer')
            ->where('qr_token', $token)
            ->with(['items.servicePricing.service', 'items.statusLogs.productionStatus'])
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $this->formatOrder($order),
        ]);
    }

    public function verify(Request $request, $token)
    {
        $request->validate(['pin' => 'required|string']);

        $order = Order::with('customer')->where('qr_token', $token)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if ($request->pin !== $order->customer->pin) {
            return response()->json(['success' => false, 'message' => 'Invalid PIN.'], 403);
        }

        $order->load(['items.servicePricing.service', 'items.statusLogs.productionStatus']);

        return response()->json([
            'success' => true,
            'order' => $this->formatOrder($order),
        ]);
    }

    private function formatOrder(Order $order): array
    {
        $statusHistory = $order->items->flatMap->statusLogs->sortBy('created_at')->map(fn($ps) => [
            'status' => $ps->productionStatus?->name ?? '-',
            'created_at' => $ps->created_at->format('d/m/Y H:i'),
            'note' => $ps->note ?? null,
        ])->toArray();

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer->name ?? '-',
            'qr_token' => $order->qr_token,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
            'status' => $order->status,
            'status_history' => $statusHistory,
        ];
    }
}
