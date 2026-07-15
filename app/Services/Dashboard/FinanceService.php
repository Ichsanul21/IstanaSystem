<?php

namespace App\Services\Dashboard;

class FinanceService
{
    public function __construct(
        protected \App\Services\FinanceService $rootFinanceService
    ) {}

    public function getRevenueVsExpense(?int $branchId = null, ?string $period = null): array
    {
        return $this->rootFinanceService->getRevenueVsExpense($branchId, $period);
    }

    public function getProfitMargin(?int $branchId = null): float
    {
        return $this->rootFinanceService->getProfitMargin($branchId);
    }

    public function getMonthlyTrend(int $months = 6, ?int $branchId = null): array
    {
        return $this->rootFinanceService->getMonthlyTrend($months, $branchId);
    }
}
