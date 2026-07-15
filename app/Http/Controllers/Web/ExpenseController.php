<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\JournalEntry;
use App\Services\Finance\JournalService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(
        protected JournalService $journalService
    ) {}

    public function index()
    {
        $expenses = Expense::forCurrentBranch()
            ->when(request('category'), fn($q, $v) => $q->where('category', $v))
            ->when(request('date_from'), fn($q, $v) => $q->whereDate('posted_at', '>=', $v))
            ->when(request('date_to'), fn($q, $v) => $q->whereDate('posted_at', '<=', $v))
            ->latest()
            ->paginate(15);

        $categories = Expense::forCurrentBranch()->select('category')->distinct()->pluck('category');

        return view('finance.expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        return view('finance.expenses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0',
            'posted_at' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $data['branch_id'] = currentBranchId();
        $data['created_by'] = auth()->id();

        $expense = Expense::create($data);

        $this->createExpenseJournalEntry($expense);

        return redirect()->route('admin.finance.expenses.index')->with('success', 'Biaya berhasil dicatat.');
    }

    public function edit(Expense $expense)
    {
        return view('finance.expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'category' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0',
            'posted_at' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $expense->update($data);

        return redirect()->route('admin.finance.expenses.index')->with('success', 'Biaya berhasil diperbarui.');
    }

    public function destroy(Expense $expense)
    {
        try {
            $this->reverseExpenseJournalEntry($expense);
            $expense->delete();
            return redirect()->route('admin.finance.expenses.index')->with('success', 'Biaya berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.finance.expenses.index')->with('error', 'Biaya gagal dihapus.');
        }
    }

    protected function createExpenseJournalEntry(Expense $expense, bool $isUpdate = false): void
    {
        try {
            $expenseAccount = ChartOfAccount::where('type', 'expense')
                ->where('is_active', true)
                ->first();

            $assetAccount = ChartOfAccount::where('type', 'asset')
                ->where('is_active', true)
                ->first();

            if (!$expenseAccount || !$assetAccount) {
                return;
            }

            if ($isUpdate) {
                JournalEntry::where('description', 'Biaya: ' . $expense->description)
                    ->where('branch_id', $expense->branch_id)
                    ->whereDate('posted_at', $expense->posted_at ?? today())
                    ->delete();
            }

            $this->journalService->createEntry(
                'Biaya: ' . $expense->description,
                [
                    ['account_id' => $expenseAccount->id, 'debit' => $expense->amount, 'credit' => 0],
                    ['account_id' => $assetAccount->id, 'debit' => 0, 'credit' => $expense->amount],
                ],
                $expense->branch_id
            );
        } catch (\Exception $e) {
            report($e);
        }
    }

    protected function reverseExpenseJournalEntry(Expense $expense): void
    {
        try {
            JournalEntry::where('description', 'Biaya: ' . $expense->description)
                ->where('branch_id', $expense->branch_id)
                ->whereDate('posted_at', $expense->posted_at ?? today())
                ->delete();
        } catch (\Exception $e) {
            report($e);
        }
    }
}
