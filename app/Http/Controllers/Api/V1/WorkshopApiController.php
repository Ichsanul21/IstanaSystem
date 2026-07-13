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
            ->with(['service', 'order.customer', 'order.items.service', 'statusLogs.productionStatus'])
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
                        ? ['code' => $currentStatus->value, 'name' => $currentStatus->label(), 'sequence' => $currentStatus->sequence()]
                        : null,
                    'next_status' => !empty($allowedTransitions)
                        ? ['code' => $allowedTransitions[0]->value, 'name' => $allowedTransitions[0]->label(), 'sequence' => $allowedTransitions[0]->sequence()]
                        : null,
                ],
                'order' => [
                    'id' => $orderItem->order_id,
                    'order_number' => $orderItem->order->order_number,
                    'customer_name' => $orderItem->order->customer?->name ?? $orderItem->order->customer_name,
                    'items' => $orderItem->order->items->map(fn($i) => [
                        'service' => ['code' => $i->service?->code ?? ''],
                        'quantity' => $i->quantity,
                        'status' => $this->workshopService->getCurrentStatus($i)?->value ?? 'Terima',
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

        if (empty($allowedTransitions)) {
            return response()->json(['success' => false, 'message' => 'No valid next status'], 422);
        }

        $nextStatus = $allowedTransitions[0];
        $userId = $request->user()?->id ?? 1;

        $status = $this->transitionService->transition($orderItem, $nextStatus, $userId, $request->note);

        $waLink = $this->trackingService->getWaLink($orderItem->order);

        return response()->json([
            'success' => true,
            'data' => [
                'new_status' => ['code' => $nextStatus->value, 'name' => $nextStatus->label(), 'sequence' => $nextStatus->sequence()],
                'wa_link' => $waLink ?? '',
                'wa_required' => in_array($nextStatus->value, ['SIAP', 'DIAMBIL']),
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
            'current_status' => $item->statusLogs->first()?->productionStatus?->code ?? 'TERIMA',
            'customer' => $item->order?->customer?->name ?? $item->order?->customer_name ?? '',
            'elapsed_time' => $item->created_at?->diffForHumans(short: true) ?? '',
        ])]);
    }

    public function stats()
    {
        $branchId = currentBranchId();

        $totalInProduction = OrderItem::whereHas('statusLogs')
            ->whereDoesntHave('statusLogs.productionStatus', fn($q) => $q->where('code', 'DIAMBIL'))
            ->count();

        $completedToday = OrderItem::whereHas('statusLogs.productionStatus', fn($q) => $q
            ->where('code', 'DIAMBIL')
            ->whereDate('order_item_status_logs.created_at', today())
        )->count();

        $statusCounts = [
            'TERIMA' => OrderItem::whereHas('statusLogs.productionStatus', fn($q) => $q->where('code', 'TERIMA'))->count(),
            'CUCI' => OrderItem::whereHas('statusLogs.productionStatus', fn($q) => $q->where('code', 'CUCI'))->count(),
            'KERING' => OrderItem::whereHas('statusLogs.productionStatus', fn($q) => $q->where('code', 'KERING'))->count(),
            'LIPAT' => OrderItem::whereHas('statusLogs.productionStatus', fn($q) => $q->where('code', 'LIPAT'))->count(),
            'CEK' => OrderItem::whereHas('statusLogs.productionStatus', fn($q) => $q->where('code', 'CEK'))->count(),
            'SIAP' => OrderItem::whereHas('statusLogs.productionStatus', fn($q) => $q->where('code', 'SIAP'))->count(),
        ];

        return response()->json(['data' => [
            'total_in_production' => $totalInProduction,
            'completed_today' => $completedToday,
            'average_time' => '3h 12m',
            'by_status' => $statusCounts,
        ]]);
    }
}
