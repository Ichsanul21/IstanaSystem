<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use App\Services\Dashboard\RevenueService;
use App\Services\Dashboard\ProductionService;
use App\Services\Dashboard\FinanceService;
use App\Services\Dashboard\InventoryService;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
        protected RevenueService $revenueService,
        protected ProductionService $productionService,
        protected FinanceService $financeService,
        protected InventoryService $inventoryService
    ) {}

    public function summary(Request $request)
    {
        $branchId = $request->branch_id ?? currentBranchId();
        $metrics = $this->dashboardService->getMetrics($branchId);
        $revenueTrend = $this->revenueService->getRevenueTrend(7, $branchId);

        $productionQueue = $this->productionService->getQueuePerStatus($branchId);
        $statusLabels = array_keys($productionQueue);
        $statusData = array_values($productionQueue);

        $topServices = $this->revenueService->getRevenueByService($branchId);

        return ApiResponse::success([
            'metrics' => $metrics,
            'revenue_trend' => $revenueTrend,
            'order_status' => [
                'labels' => $statusLabels,
                'data' => $statusData,
            ],
            'top_services' => $topServices,
        ]);
    }

    public function revenue(Request $request)
    {
        $branchId = $request->branch_id ?? currentBranchId();

        return ApiResponse::success([
            'by_service' => $this->revenueService->getRevenueByService($branchId),
            'by_branch' => $this->revenueService->getRevenueByBranch(),
            'trend' => $this->revenueService->getRevenueTrend(30, $branchId),
        ]);
    }

    public function operational(Request $request)
    {
        $branchId = $request->branch_id ?? currentBranchId();
        $metrics = $this->dashboardService->getMetrics($branchId);
        $peakHours = $this->dashboardService->getPeakHours($branchId);
        $topCustomers = $this->dashboardService->getTopCustomers($branchId);
        $avgOrderValue = $this->dashboardService->getAverageOrderValue($branchId);

        return ApiResponse::success([
            'metrics' => $metrics,
            'peak_hours' => $peakHours,
            'top_customers' => $topCustomers,
            'average_order_value' => $avgOrderValue,
        ]);
    }

    public function production(Request $request)
    {
        $branchId = $request->branch_id ?? currentBranchId();

        return ApiResponse::success([
            'queue_by_status' => $this->productionService->getQueuePerStatus($branchId),
            'average_processing_time' => $this->productionService->getAverageProcessingTime($branchId),
            'items_in_production' => $this->productionService->getItemsInProduction($branchId),
        ]);
    }

    public function financeData(Request $request)
    {
        $branchId = $request->branch_id ?? currentBranchId();

        return ApiResponse::success([
            'revenue_vs_expense' => $this->financeService->getRevenueVsExpense($branchId),
            'profit_margin' => $this->financeService->getProfitMargin($branchId),
            'monthly_trend' => $this->financeService->getMonthlyTrend(12, $branchId),
        ]);
    }

    public function inventoryData(Request $request)
    {
        $branchId = $request->branch_id ?? currentBranchId();

        return ApiResponse::success([
            'stock_value' => $this->inventoryService->getStockValue($branchId),
            'low_stock_alerts' => $this->inventoryService->getLowStockItems($branchId),
            'recent_movements' => $this->inventoryService->getStockMovement($branchId),
        ]);
    }
}
