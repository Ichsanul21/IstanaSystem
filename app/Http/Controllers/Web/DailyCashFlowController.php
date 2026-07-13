<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DailyCashFlow;
use Illuminate\Http\Request;

class DailyCashFlowController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $todayCashFlow = DailyCashFlow::forCurrentBranch()->where('date', $today)->first();
        $todayPayments = \App\Models\Payment::whereHas('order', fn($q) => $q->forCurrentBranch())
            ->whereDate('paid_at', $today)
            ->with('order')
            ->get();

        $cashFlows = DailyCashFlow::forCurrentBranch()
            ->when(request('date_from'), fn($q, $v) => $q->where('date', '>=', $v))
            ->when(request('date_to'), fn($q, $v) => $q->where('date', '<=', $v))
            ->latest('date')
            ->paginate(15);

        $totalCashIn = $todayPayments->sum('amount');
        $totalCashOut = 0; // would come from expense/purchase records
        $cashInCount = $todayPayments->count();
        $cashOutCount = 0;

        return view('cash-flow.index', compact('cashFlows', 'todayCashFlow', 'totalCashIn', 'totalCashOut', 'cashInCount', 'cashOutCount', 'today'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'opening_balance' => 'nullable|numeric|min:0',
            'closing_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        DailyCashFlow::create(array_merge($data, [
            'branch_id' => currentBranchId(),
            'total_cash_in' => 0,
            'total_cash_out' => 0,
        ]));

        return redirect()->route('admin.cash-flow.index')->with('success', 'Arus kas berhasil dicatat.');
    }
}
