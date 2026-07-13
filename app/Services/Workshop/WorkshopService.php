<?php

namespace App\Services\Workshop;

use App\Enums\OrderStatus;
use App\Enums\ProductionStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemStatusLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WorkshopService
{
    public function getCurrentStatus(OrderItem $item): ?ProductionStatus
    {
        $latest = $item->statusLogs()->latest()->first();

        if (!$latest) {
            return null;
        }

        $statusCode = $latest->productionStatus?->code;

        return $statusCode ? ProductionStatus::tryFrom($statusCode) : null;
    }

    public function getAllowedTransitions(OrderItem $item): array
    {
        $current = $this->getCurrentStatus($item);

        return ProductionStatus::allowedTransitionsFrom($current);
    }

    public function getOrderProgress(Order $order): array
    {
        $order->loadMissing('items.statusLogs.productionStatus');

        $progress = [];
        foreach ($order->items as $item) {
            $currentStatus = $this->getCurrentStatus($item);
            $progress[$item->id] = [
                'item' => $item,
                'current_status' => $currentStatus,
                'sequence' => $currentStatus?->sequence() ?? 0,
                'label' => $currentStatus?->label() ?? 'Belum Diproses',
                'allowed_next' => $this->getAllowedTransitions($item),
            ];
        }

        return $progress;
    }

    public function getWorkshopQueue(?int $branchId = null): LengthAwarePaginator
    {
        $query = OrderItem::query()
            ->whereHas('order', function ($q) use ($branchId) {
                $q->whereNotIn('status', [OrderStatus::Completed->value, OrderStatus::Cancelled->value]);
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
            })
            ->with(['order.customer', 'order.branch', 'statusLogs' => fn($q) => $q->latest()]);

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }
}
