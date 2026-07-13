<?php

namespace App\Services\Workshop;

use App\Enums\OrderStatus;
use App\Enums\ProductionStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemStatusLog;
use App\Models\ProductionStatus as ProductionStatusModel;

class StatusTransitionService
{
    public function __construct(
        protected WorkshopService $workshopService
    ) {}

    public function validateTransition(OrderItem $item, string $toStatus): bool
    {
        $currentStatus = $this->workshopService->getCurrentStatus($item);
        $targetStatus = ProductionStatus::tryFrom($toStatus);

        if (!$targetStatus) {
            return false;
        }

        $allowed = ProductionStatus::allowedTransitionsFrom($currentStatus);

        return in_array($targetStatus, $allowed, true);
    }

    public function transition(OrderItem $item, string $toStatus, int $userId, ?string $notes = null): ProductionStatus
    {
        $targetStatus = ProductionStatus::tryFrom($toStatus);

        if (!$targetStatus) {
            throw new InvalidStatusTransitionException("Status '{$toStatus}' tidak dikenali.");
        }

        if (!$this->validateTransition($item, $toStatus)) {
            $currentStatus = $this->workshopService->getCurrentStatus($item);
            $currentLabel = $currentStatus?->label() ?? 'Belum Diproses';
            throw new InvalidStatusTransitionException(
                "Transisi dari '{$currentLabel}' ke '{$targetStatus->label()}' tidak diizinkan."
            );
        }

        $currentStatus = $this->workshopService->getCurrentStatus($item);
        $fromCode = $currentStatus?->value;

        $productionStatus = ProductionStatusModel::where('code', $targetStatus->value)->first();

        OrderItemStatusLog::create([
            'order_item_id' => $item->id,
            'production_status_id' => $productionStatus?->id,
            'note' => $notes,
            'scanned_by' => $userId,
            'scan_time' => now(),
        ]);

        $this->updateOrderStatus($item->order);

        return $targetStatus;
    }

    public function updateOrderStatus(Order $order): void
    {
        $order->loadMissing('items.statusLogs.productionStatus');

        $allTerminal = true;
        $anyProcessing = false;

        foreach ($order->items as $item) {
            $status = $this->workshopService->getCurrentStatus($item);

            if ($status === null || $status === ProductionStatus::Terima) {
                $allTerminal = false;
            }

            if ($status && !$status->isTerminal()) {
                $anyProcessing = true;
            }
        }

        if ($allTerminal) {
            $order->update([
                'status' => OrderStatus::Completed->value,
                'finished_at' => $order->finished_at ?? now(),
            ]);
        } elseif ($anyProcessing) {
            $order->update(['status' => OrderStatus::Processing->value]);
        }
    }
}
