<?php

namespace App\Services\Dashboard;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class RevenueService
{
    public function getRevenueTrend(int $days = 7, ?int $branchId = null): array
    {
        $results = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $revenue = Payment::whereDate('paid_at', $date)
                ->when($branchId, fn($q) => $q->whereHas('order', fn($oq) => $oq->where('branch_id', $branchId)))
                ->sum('amount');
            $results[] = ['date' => $date, 'revenue' => (float) $revenue];
        }
        return $results;
    }

    public function getRevenueByService(?int $branchId = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        return OrderItem::select('service_name', DB::raw('COALESCE(SUM(subtotal), 0) as total'))
            ->whereHas('order', function ($q) use ($branchId, $dateFrom, $dateTo) {
                $q->when($branchId, fn($qq) => $qq->where('branch_id', $branchId))
                    ->when($dateFrom, fn($qq) => $qq->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($qq) => $qq->whereDate('created_at', '<=', $dateTo))
                    ->whereIn('status', ['completed', 'processing']);
            })
            ->groupBy('service_name')
            ->pluck('total', 'service_name')
            ->toArray();
    }

    public function getRevenueByBranch(): array
    {
        return Branch::where('is_active', true)
            ->get()
            ->map(function ($branch) {
                $revenue = Payment::whereHas('order', fn($q) => $q->where('branch_id', $branch->id))
                    ->whereNotNull('paid_at')
                    ->sum('amount');
                return ['branch' => $branch->name, 'revenue' => (float) $revenue];
            })
            ->toArray();
    }

    public function getPaymentMethodBreakdown(?int $branchId = null): array
    {
        return Payment::select('payment_method', DB::raw('COALESCE(SUM(amount), 0) as total'), DB::raw('COUNT(*) as count'))
            ->whereNotNull('paid_at')
            ->when($branchId, fn($q) => $q->whereHas('order', fn($oq) => $oq->where('branch_id', $branchId)))
            ->groupBy('payment_method')
            ->get()
            ->toArray();
    }
}
