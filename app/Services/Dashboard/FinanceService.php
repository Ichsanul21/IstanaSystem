<?php

namespace App\Services\Dashboard;

use App\Models\ChartOfAccount;
use App\Models\DailyCashFlow;
use App\Models\JournalEntryLine;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    public function getRevenueVsExpense(?int $branchId = null, ?string $period = null): array
    {
        $query = DailyCashFlow::query();
        if ($branchId) $query->where('branch_id', $branchId);

        if ($period === 'daily') {
            $query->whereDate('date', today());
        } elseif ($period === 'weekly') {
            $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period === 'monthly') {
            $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
        } elseif ($period === 'yearly') {
            $query->whereYear('date', now()->year);
        }

        $totals = $query->select(
            DB::raw('COALESCE(SUM(total_revenue), 0) as revenue'),
            DB::raw('COALESCE(SUM(total_expense), 0) as expense')
        )->first();

        return [
            'revenue' => (float) ($totals->revenue ?? 0),
            'expense' => (float) ($totals->expense ?? 0),
        ];
    }

    public function getProfitMargin(?int $branchId = null): float
    {
        $revenue = Payment::whereNotNull('paid_at')
            ->when($branchId, fn($q) => $q->whereHas('order', fn($oq) => $oq->where('branch_id', $branchId)))
            ->sum('amount');

        $expense = DailyCashFlow::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('total_expense');

        if ($revenue <= 0) return 0;

        return round((($revenue - $expense) / $revenue) * 100, 2);
    }

    public function getMonthlyTrend(int $months = 6, ?int $branchId = null): array
    {
        $results = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = Payment::whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->when($branchId, fn($q) => $q->whereHas('order', fn($oq) => $oq->where('branch_id', $branchId)))
                ->sum('amount');

            $expense = DailyCashFlow::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->sum('total_expense');

            $results[] = [
                'month' => $date->format('Y-m'),
                'revenue' => (float) $revenue,
                'expense' => (float) $expense,
            ];
        }
        return $results;
    }
}
