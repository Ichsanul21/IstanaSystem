<?php

namespace App\Services\Dashboard;

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

        $pendingOrders = (clone $query)->whereNotIn('status', ['completed', 'cancelled'])->count();

        $totalCustomers = Customer::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();

        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        return compact('totalOrders', 'totalRevenue', 'pendingOrders', 'totalCustomers', 'avgOrderValue');
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
