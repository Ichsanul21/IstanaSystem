<?php

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index(Request $request)
    {
        $branchId = $request->has('branch_id') && auth()->user()?->hasRole(['Developer', 'Super Admin'])
            ? $request->branch_id
            : currentBranchId();

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $metrics = $this->dashboardService->getMetrics($branchId, $dateFrom, $dateTo);

        $revenueTrend = $this->dashboardService->getRevenueService()->getRevenueTrend(7, $branchId);
        $revenueByService = $this->dashboardService->getRevenueService()->getRevenueByService($branchId, $dateFrom, $dateTo);
        $paymentMethods = $this->dashboardService->getRevenueService()->getPaymentMethodBreakdown($branchId);

        $itemsInProduction = $this->dashboardService->getProductionService()->getItemsInProduction($branchId);
        $queuePerStatus = $this->dashboardService->getProductionService()->getQueuePerStatus($branchId);
        $avgProcessingTime = $this->dashboardService->getProductionService()->getAverageProcessingTime($branchId);

        $revenueVsExpense = $this->dashboardService->getFinanceService()->getRevenueVsExpense($branchId);
        $profitMargin = $this->dashboardService->getFinanceService()->getProfitMargin($branchId);
        $monthlyTrend = $this->dashboardService->getFinanceService()->getMonthlyTrend(6, $branchId);

        $stockValue = $this->dashboardService->getInventoryService()->getStockValue($branchId);
        $lowStockItems = $this->dashboardService->getInventoryService()->getLowStockItems($branchId);
        $stockMovement = $this->dashboardService->getInventoryService()->getStockMovement($branchId);

        $recentOrders = Order::with('customer')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->take(10)
            ->get();

        $orderStatusDistribution = Order::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $topCustomers = \App\Models\Customer::withSum(['orders as total_spent' => function ($q) use ($branchId) {
            $q->when($branchId, fn($qq) => $qq->where('branch_id', $branchId))
                ->whereIn('status', [OrderStatus::Completed->value, OrderStatus::Processing->value]);
        }], 'grand_total')
            ->whereHas('orders', function ($q) use ($branchId) {
                $q->when($branchId, fn($qq) => $qq->where('branch_id', $branchId));
            })
            ->orderByDesc('total_spent')
            ->take(5)
            ->get()
            ->map(fn($c) => [
                'name' => $c->name,
                'order_count' => $c->orders()->when($branchId, fn($q) => $q->where('branch_id', $branchId))->count(),
                'total_spent' => (float) ($c->total_spent ?? 0),
            ])
            ->toArray();

        $branches = auth()->user()?->hasRole(['Developer', 'Super Admin'])
            ? Branch::where('is_active', true)->get()
            : collect();

        $activeTab = $request->tab ?? 'pendapatan';

        return view('dashboard', compact(
            'metrics',
            'revenueTrend',
            'revenueByService',
            'paymentMethods',
            'itemsInProduction',
            'queuePerStatus',
            'avgProcessingTime',
            'revenueVsExpense',
            'profitMargin',
            'monthlyTrend',
            'stockValue',
            'lowStockItems',
            'stockMovement',
            'recentOrders',
            'orderStatusDistribution',
            'topCustomers',
            'branches',
            'branchId',
            'dateFrom',
            'dateTo',
            'activeTab',
        ));
    }
}
