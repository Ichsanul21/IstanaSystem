<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Services\Workshop\WorkshopService;
use App\Services\Workshop\StatusTransitionService;
use App\Services\Tracking\TrackingService;
use Illuminate\Http\Request;

class WorkshopApiController extends Controller
{
    public function __construct(
        protected WorkshopService $workshopService,
        protected StatusTransitionService $transitionService,
        protected TrackingService $trackingService
    ) {}

    public function scan($qrToken)
    {
        $orderItem = OrderItem::where('qr_token', $qrToken)
            ->with(['service', 'order.customer', 'order.items.service', 'orderItemStatusLogs'])
            ->firstOrFail();

        $currentStatus = $this->workshopService->getCurrentStatus($orderItem);
        $allowedTransitions = $this->workshopService->getAllowedTransitions($orderItem);

        return response()->json([
            'success' => true,
            'data' => [
                'item' => [
                    'id' => $orderItem->id,
                    'service' => $orderItem->service
                        ? ['code' => $orderItem->service->code, 'name' => $orderItem->service->name]
                        : ['code' => '', 'name' => ''],
                    'quantity' => $orderItem->quantity,
                    'current_status' => $currentStatus
                        ? ['id' => $currentStatus->id, 'code' => $currentStatus->to_status, 'name' => $currentStatus->to_status, 'sequence' => $currentStatus->id]
                        : null,
                    'next_status' => $allowedTransitions->first()
                        ? ['id' => 0, 'code' => $allowedTransitions->first(), 'name' => $allowedTransitions->first(), 'sequence' => 0]
                        : null,
                ],
                'order' => [
                    'id' => $orderItem->order_id,
                    'order_number' => $orderItem->order->order_number,
                    'customer_name' => $orderItem->order->customer?->name ?? $orderItem->order->customer_name,
                    'items' => $orderItem->order->items->map(fn($i) => [
                        'service' => ['code' => $i->service?->code ?? ''],
                        'quantity' => $i->quantity,
                        'status' => $this->workshopService->getCurrentStatus($i)?->to_status ?? 'received',
                    ]),
                ],
            ],
        ]);
    }

    public function updateScanStatus(Request $request, $qrToken)
    {
        $orderItem = OrderItem::where('qr_token', $qrToken)->firstOrFail();

        $request->validate(['note' => 'nullable|string|max:500']);

        $allowedTransitions = $this->workshopService->getAllowedTransitions($orderItem);

        if ($allowedTransitions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No valid next status'], 422);
        }

        $nextStatus = $allowedTransitions->first();
        $userId = $request->user()?->id ?? 1;

        $status = $this->transitionService->transition($orderItem, $nextStatus, $userId, $request->note);

        $waLink = $this->trackingService->getWaLink($orderItem->order);

        return response()->json([
            'success' => true,
            'data' => [
                'new_status' => ['code' => $status->to_status, 'name' => $status->to_status, 'sequence' => $status->id],
                'wa_link' => $waLink ?? '',
                'wa_required' => in_array($nextStatus, ['ready_for_pickup', 'picked_up']),
            ],
        ]);
    }

    public function queue(Request $request)
    {
        $queue = $this->workshopService->getWorkshopQueue(currentBranchId());

        if ($request->filled('status')) {
            $queue = $queue->filter(function ($item) use ($request) {
                return true;
            });
        }

        return response()->json(['data' => $queue->map(fn($item) => [
            'order_number' => $item->order?->order_number ?? '',
            'item' => (string) ($item->service?->name ?? '') . ' - ' . $item->quantity . ' kg',
            'current_status' => $item->latestStatus?->to_status ?? 'received',
            'customer' => $item->order?->customer?->name ?? $item->order?->customer_name ?? '',
            'elapsed_time' => $item->created_at?->diffForHumans(short: true) ?? '',
        ])]);
    }

    public function stats()
    {
        $branchId = currentBranchId();

        $totalInProduction = OrderItem::whereHas('orderItemStatusLogs')
            ->whereDoesntHave('orderItemStatusLogs', fn($q) => $q->where('to_status', 'picked_up'))
            ->count();

        $completedToday = OrderItem::whereHas('orderItemStatusLogs', fn($q) => $q
            ->where('to_status', 'picked_up')
            ->whereDate('created_at', today())
        )->count();

        $statusCounts = [
            'received' => OrderItem::whereHas('orderItemStatusLogs', fn($q) => $q->where('to_status', 'received'))->count(),
            'washed' => OrderItem::whereHas('orderItemStatusLogs', fn($q) => $q->where('to_status', 'washed'))->count(),
            'dried' => OrderItem::whereHas('orderItemStatusLogs', fn($q) => $q->where('to_status', 'dried'))->count(),
            'ironed' => OrderItem::whereHas('orderItemStatusLogs', fn($q) => $q->where('to_status', 'ironed'))->count(),
            'packed' => OrderItem::whereHas('orderItemStatusLogs', fn($q) => $q->where('to_status', 'packed'))->count(),
            'ready_for_pickup' => OrderItem::whereHas('orderItemStatusLogs', fn($q) => $q->where('to_status', 'ready_for_pickup'))->count(),
        ];

        return response()->json(['data' => [
            'total_in_production' => $totalInProduction,
            'completed_today' => $completedToday,
            'average_time' => '3h 12m',
            'by_status' => $statusCounts,
        ]]);
    }
}
