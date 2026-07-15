<?php

namespace App\Services\Dashboard;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;

class DashboardService
{
    public function __construct(
        private RevenueService $revenueService,
        private ProductionService $productionService,
        private FinanceService $financeService,
        private InventoryService $inventoryService,
    ) {}

    public function getMetrics(?int $branchId = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = Order::query();
        if ($branchId) $query->where('branch_id', $branchId);
        if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo) $query->whereDate('created_at', '<=', $dateTo);

        $totalOrders = (clone $query)->count();

        $paymentQuery = Payment::whereNotNull('paid_at')
            ->whereHas('order', function ($q) use ($branchId, $dateFrom, $dateTo) {
                if ($branchId) $q->where('branch_id', $branchId);
                if ($dateFrom) $q->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo) $q->whereDate('created_at', '<=', $dateTo);
            });
        $totalRevenue = (float) $paymentQuery->sum('amount');

        $pendingOrders = (clone $query)->whereNotIn('status', [OrderStatus::ReadyForPickup->value, OrderStatus::PickedUp->value, OrderStatus::Cancelled->value])->count();

        $totalCustomers = Customer::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();

        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        return compact('totalOrders', 'totalRevenue', 'pendingOrders', 'totalCustomers', 'avgOrderValue');
    }

    public function getPeakHours(?int $branchId = null): array
    {
        $orders = Order::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->selectRaw("strftime('%H', created_at) as hour, COUNT(*) as total")
            ->groupBy('hour')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return $orders->map(fn($o) => [
            'hour' => $o->hour . ':00',
            'total' => (int) $o->total,
        ]);
    }

    public function getTopCustomers(?int $branchId = null, int $limit = 5): array
    {
        return Order::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select('customer_id')
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('COALESCE(SUM(grand_total), 0) as total_spent')
            ->whereNotNull('customer_id')
            ->groupBy('customer_id')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->with('customer:id,name,phone')
            ->get()
            ->map(fn($o) => [
                'name' => $o->customer?->name ?? 'Unknown',
                'phone' => $o->customer?->phone ?? '',
                'total_orders' => (int) $o->total_orders,
                'total_spent' => (float) $o->total_spent,
            ])
            ->toArray();
    }

    public function getAverageOrderValue(?int $branchId = null): float
    {
        $orders = Order::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('payment_status', 'paid');

        $count = (clone $orders)->count();

        return $count > 0 ? round((clone $orders)->sum('grand_total') / $count, 2) : 0;
    }

    public function getRevenueService(): RevenueService
    {
        return $this->revenueService;
    }

    public function getProductionService(): ProductionService
    {
        return $this->productionService;
    }

    public function getFinanceService(): FinanceService
    {
        return $this->financeService;
    }

    public function getInventoryService(): InventoryService
    {
        return $this->inventoryService;
    }
}
