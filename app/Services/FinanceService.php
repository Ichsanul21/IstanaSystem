<?php

namespace App\Services;

use App\Models\AccountingPeriod;
use App\Models\DailyCashFlow;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    public function createJournalEntry(string $description, array $lines, ?int $branchId = null): JournalEntry
    {
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($lines as $line) {
            $totalDebit += $line['debit'] ?? 0;
            $totalCredit += $line['credit'] ?? 0;
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new \InvalidArgumentException('Debit and credit totals must be equal.');
        }

        return DB::transaction(function () use ($description, $lines, $branchId) {
            $entryNumber = $this->generateEntryNumber();

            $period = AccountingPeriod::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where('is_closed', false)
                ->first();

            $entry = JournalEntry::create([
                'entry_number' => $entryNumber,
                'description' => $description,
                'period_id' => $period?->id,
                'branch_id' => $branchId,
                'created_by' => Auth::id(),
                'entry_date' => now(),
            ]);

            foreach ($lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? null,
                ]);
            }

            return $entry;
        });
    }

    private function generateEntryNumber(): string
    {
        $date = now()->format('Ymd');
        $last = JournalEntry::whereDate('created_at', today())->lockForUpdate()->count();

        return 'JE-' . $date . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }

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
