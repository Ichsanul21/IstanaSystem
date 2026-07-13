<?php

namespace App\Services\Dashboard;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class ProductionService
{
    public function getItemsInProduction(?int $branchId = null): int
    {
        return OrderItem::whereHas('order', function ($q) use ($branchId) {
            $q->when($branchId, fn($qq) => $qq->where('branch_id', $branchId))
                ->whereNotIn('status', [OrderStatus::Completed->value, OrderStatus::Cancelled->value]);
        })->count();
    }

    public function getQueuePerStatus(?int $branchId = null): array
    {
        $statuses = ['received', 'washed', 'dried', 'ironed', 'packed', 'ready_for_pickup'];
        $result = [];

        $items = OrderItem::select('order_items.id')
            ->addSelect(DB::raw('(SELECT ps.code FROM order_item_status_logs oisl JOIN production_statuses ps ON ps.id = oisl.production_status_id WHERE oisl.order_item_id = order_items.id ORDER BY oisl.id DESC LIMIT 1) as current_status'))
            ->whereHas('order', function ($q) use ($branchId) {
                $q->when($branchId, fn($qq) => $qq->where('branch_id', $branchId))
                    ->whereNotIn('status', [OrderStatus::Completed->value, OrderStatus::Cancelled->value]);
            })
            ->get();

        $grouped = $items->groupBy('current_status');

        foreach ($statuses as $status) {
            $result[$status] = $grouped->get($status)?->count() ?? 0;
        }

        return $result;
    }

    public function getAverageProcessingTime(?int $branchId = null): float
    {
        $items = OrderItem::whereHas('order', function ($q) use ($branchId) {
            $q->when($branchId, fn($qq) => $qq->where('branch_id', $branchId))
                ->where('status', OrderStatus::Completed->value);
        })->whereHas('statusLogs')->get();

        $totalMinutes = 0;
        $count = 0;

        foreach ($items as $item) {
            $first = $item->statusLogs()->orderBy('created_at')->first();
            $last = $item->statusLogs()->orderByDesc('created_at')->first();
            if ($first && $last) {
                $totalMinutes += $first->created_at->diffInMinutes($last->created_at);
                $count++;
            }
        }

        return $count > 0 ? round($totalMinutes / $count, 2) : 0;
    }
}
