<?php

namespace App\Services\Finance;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalService
{
    public function createEntry(string $description, array $lines, ?int $branchId = null): JournalEntry
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

            $period = \App\Models\AccountingPeriod::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where('is_closed', false)
                ->first();

            $entry = JournalEntry::create([
                'entry_number' => $entryNumber,
                'description' => $description,
                'period_id' => $period?->id,
                'branch_id' => $branchId,
                'user_id' => Auth::id(),
                'posted_at' => now(),
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

    public function getAccountBalance(int $accountId, ?int $branchId = null): float
    {
        $query = JournalEntryLine::where('account_id', $accountId)
            ->whereHas('journalEntry', function ($q) use ($branchId) {
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
            });

        $totalDebit = (float) $query->sum('debit');
        $totalCredit = (float) $query->sum('credit');

        $account = ChartOfAccount::find($accountId);

        if (! $account) {
            return 0;
        }

        return match ($account->type) {
            'asset', 'expense' => $totalDebit - $totalCredit,
            'liability', 'equity', 'revenue' => $totalCredit - $totalDebit,
            default => $totalDebit - $totalCredit,
        };
    }

    public function getTrialBalance(?int $branchId = null): Collection
    {
        $accounts = ChartOfAccount::where('is_active', true)->get();

        $trialBalance = $accounts->map(function ($account) use ($branchId) {
            $balance = $this->getAccountBalance($account->id, $branchId);

            return [
                'account_id' => $account->id,
                'account_code' => $account->code,
                'account_name' => $account->name,
                'type' => $account->type,
                'debit' => $balance > 0 ? $balance : 0,
                'credit' => $balance < 0 ? abs($balance) : 0,
            ];
        });

        return new Collection($trialBalance);
    }

    private function generateEntryNumber(): string
    {
        $date = now()->format('Ymd');
        $last = JournalEntry::whereDate('created_at', today())->lockForUpdate()->count();

        return 'JE-' . $date . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
}
