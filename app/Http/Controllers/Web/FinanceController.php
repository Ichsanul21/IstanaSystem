<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\FinanceService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function __construct(protected FinanceService $financeService) {}

    public function index()
    {
        $journalEntries = JournalEntry::forCurrentBranch()
            ->with(['user', 'lines.account'])
            ->latest()
            ->paginate(15);

        return view('finance.dashboard', compact('journalEntries'));
    }

    public function accounts()
    {
        $accounts = ChartOfAccount::where('is_active', true)->get();

        return view('finance.accounts', compact('accounts'));
    }

    public function journal()
    {
        $entries = JournalEntry::forCurrentBranch()
            ->with(['lines.account', 'user'])
            ->latest()
            ->paginate(15);

        return view('finance.journal.index', compact('entries'));
    }

    public function createJournal()
    {
        $accounts = ChartOfAccount::where('is_active', true)->get();

        return view('finance.journal.create', compact('accounts'));
    }

    public function storeJournal(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string|max:500',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.description' => 'nullable|string|max:255',
        ]);

        $this->financeService->createJournalEntry($data['description'], $data['lines'], currentBranchId());

        return redirect()->route('admin.finance.journal')->with('success', 'Jurnal berhasil dibuat.');
    }

    public function trialBalance()
    {
        $accounts = ChartOfAccount::where('is_active', true)->get()->map(function ($account) {
            $debit = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())
                ->sum('debit');
            $credit = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())
                ->sum('credit');

            $account->balance = match ($account->category) {
                'asset', 'expense' => $debit - $credit,
                'liability', 'equity', 'revenue' => $credit - $debit,
                default => $debit - $credit,
            };

            return $account;
        });

        return view('finance.reports.trial-balance', compact('accounts'));
    }

    public function incomeStatement()
    {
        $revenueAccounts = ChartOfAccount::where('category', 'revenue')->where('is_active', true)->get();
        $expenseAccounts = ChartOfAccount::where('category', 'expense')->where('is_active', true)->get();

        $revenues = $revenueAccounts->map(function ($account) {
            $account->balance = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())
                ->sum('credit') - JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())
                ->sum('debit');

            return $account;
        });

        $expenses = $expenseAccounts->map(function ($account) {
            $account->balance = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())
                ->sum('debit') - JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())
                ->sum('credit');

            return $account;
        });

        $totalRevenue = $revenues->sum('balance');
        $totalExpense = $expenses->sum('balance');
        $netIncome = $totalRevenue - $totalExpense;

        return view('finance.reports.income-statement', compact('revenues', 'expenses', 'totalRevenue', 'totalExpense', 'netIncome'));
    }
}
