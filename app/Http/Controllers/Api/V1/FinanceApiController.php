<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Expense;
use App\Models\AccountingPeriod;
use App\Models\TaxLog;
use App\Models\TaxConfiguration;
use App\Services\FinanceService;
use App\Services\Finance\TaxService;
use Illuminate\Http\Request;

class FinanceApiController extends Controller
{
    public function __construct(
        protected FinanceService $financeService,
        protected TaxService $taxService
    ) {}

    // Journal
    public function journalIndex(Request $request)
    {
        $query = JournalEntry::forCurrentBranch()->with(['user', 'lines.account']);

        if ($request->filled('period_id')) {
            $query->where('accounting_period_id', $request->period_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return ApiResponse::paginate($query->latest()->paginate($request->per_page ?? 15));
    }

    public function journalShow($id)
    {
        $entry = JournalEntry::with(['lines.account', 'user'])->findOrFail($id);
        return ApiResponse::success($entry);
    }

    public function journalStore(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string|max:500',
            'entry_date' => 'nullable|date',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
        ]);

        $entry = $this->financeService->createJournalEntry($data['description'], $data['lines'], currentBranchId());

        return ApiResponse::success($entry, null, 201);
    }

    // COA
    public function coaIndex()
    {
        $accounts = ChartOfAccount::where('is_active', true)->get();
        return ApiResponse::success($accounts);
    }

    public function coaShow($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        $debit = JournalEntryLine::where('account_id', $id)->sum('debit');
        $credit = JournalEntryLine::where('account_id', $id)->sum('credit');

        $balance = match ($account->type) {
            'asset', 'expense' => $debit - $credit,
            'liability', 'equity', 'revenue' => $credit - $debit,
            default => $debit - $credit,
        };

        return ApiResponse::success([
            'id' => $account->id,
            'code' => $account->code,
            'name' => $account->name,
            'type' => $account->type,
            'balance' => $balance,
            'is_active' => (bool) $account->is_active,
        ]);
    }

    public function coaLedger(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);

        $query = JournalEntryLine::where('account_id', $id)
            ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch());

        if ($request->filled('date_from')) {
            $query->whereHas('journalEntry', fn($q) => $q->whereDate('created_at', '>=', $request->date_from));
        }
        if ($request->filled('date_to')) {
            $query->whereHas('journalEntry', fn($q) => $q->whereDate('created_at', '<=', $request->date_to));
        }

        return ApiResponse::success($query->with('journalEntry')->get());
    }

    // Reports
    public function trialBalance(Request $request)
    {
        $branchId = $request->branch_id ?? currentBranchId();

        $accounts = ChartOfAccount::where('is_active', true)->get()->map(function ($account) use ($branchId) {
            $debit = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())
                ->sum('debit');
            $credit = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())
                ->sum('credit');
            $account->balance = match ($account->type) {
                'asset', 'expense' => $debit - $credit,
                'liability', 'equity', 'revenue' => $credit - $debit,
                default => $debit - $credit,
            };
            return $account;
        });

        return ApiResponse::success($accounts);
    }

    public function profitLoss(Request $request)
    {
        $branchId = $request->branch_id ?? currentBranchId();

        $revenue = ChartOfAccount::where('type', 'revenue')->where('is_active', true)->get()->map(function ($a) use ($branchId) {
            $a->balance = JournalEntryLine::where('account_id', $a->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())->sum('credit')
                - JournalEntryLine::where('account_id', $a->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())->sum('debit');
            return $a;
        });

        $expenses = ChartOfAccount::where('type', 'expense')->where('is_active', true)->get()->map(function ($a) use ($branchId) {
            $a->balance = JournalEntryLine::where('account_id', $a->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())->sum('debit')
                - JournalEntryLine::where('account_id', $a->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())->sum('credit');
            return $a;
        });

        return ApiResponse::success([
            'revenue' => $revenue,
            'total_revenue' => $revenue->sum('balance'),
            'expenses' => $expenses,
            'total_expenses' => $expenses->sum('balance'),
            'net_income' => $revenue->sum('balance') - $expenses->sum('balance'),
        ]);
    }

    public function balanceSheet()
    {
        $assets = ChartOfAccount::where('type', 'asset')->where('is_active', true)->get()->map(function ($a) {
            $a->balance = JournalEntryLine::where('account_id', $a->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())->sum('debit')
                - JournalEntryLine::where('account_id', $a->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())->sum('credit');
            return $a;
        });

        $liabilities = ChartOfAccount::where('type', 'liability')->where('is_active', true)->get()->map(function ($a) {
            $a->balance = JournalEntryLine::where('account_id', $a->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())->sum('credit')
                - JournalEntryLine::where('account_id', $a->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())->sum('debit');
            return $a;
        });

        $equity = ChartOfAccount::where('type', 'equity')->where('is_active', true)->get()->map(function ($a) {
            $a->balance = JournalEntryLine::where('account_id', $a->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())->sum('credit')
                - JournalEntryLine::where('account_id', $a->id)
                ->whereHas('journalEntry', fn($q) => $q->forCurrentBranch())->sum('debit');
            return $a;
        });

        return ApiResponse::success([
            'assets' => ['accounts' => $assets, 'total' => $assets->sum('balance')],
            'liabilities' => ['accounts' => $liabilities, 'total' => $liabilities->sum('balance')],
            'equity' => ['accounts' => $equity, 'total' => $equity->sum('balance')],
        ]);
    }

    // Expenses
    public function expensesIndex(Request $request)
    {
        $query = Expense::forCurrentBranch();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        return ApiResponse::paginate($query->latest()->paginate($request->per_page ?? 15));
    }

    public function expensesStore(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $data['branch_id'] = currentBranchId();
        $data['user_id'] = $request->user()->id;
        $expense = Expense::create($data);

        return ApiResponse::success($expense, null, 201);
    }

    // Tax
    public function taxSummary(Request $request)
    {
        $period = $request->period ?? date('Y-m');
        $regime = $request->regime ?? 'pp23';

        $config = TaxConfiguration::where('regime', $regime)->first();

        $logs = TaxLog::where('period', $period)->get();

        return ApiResponse::success([
            'regime' => $regime,
            'period' => $period,
            'rate' => $config?->rate ?? 0,
            'total_tax' => $logs->sum('amount'),
            'logs' => $logs,
        ]);
    }

    // Periods
    public function periodsIndex()
    {
        return ApiResponse::success(AccountingPeriod::orderBy('start_date', 'desc')->get());
    }

    public function periodsStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $period = AccountingPeriod::create($data);

        return ApiResponse::success($period, null, 201);
    }

    public function periodsClose($id)
    {
        $period = AccountingPeriod::findOrFail($id);
        $period->update(['is_closed' => true, 'closed_at' => now()]);

        return ApiResponse::success(null, 'Period closed');
    }
}
