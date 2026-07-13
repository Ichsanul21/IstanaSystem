<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AccountingPeriod;
use Illuminate\Http\Request;

class AccountingPeriodController extends Controller
{
    public function index()
    {
        $periods = AccountingPeriod::latest()->paginate(15);

        return view('finance.periods.index', compact('periods'));
    }

    public function create()
    {
        return view('finance.periods.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_closed' => 'boolean',
        ]);

        AccountingPeriod::create($data);

        return redirect()->route('admin.finance.periods.index')->with('success', 'Periode akuntansi berhasil ditambahkan.');
    }

    public function edit(AccountingPeriod $accountingPeriod)
    {
        return view('finance.periods.edit', ['period' => $accountingPeriod]);
    }

    public function update(Request $request, AccountingPeriod $accountingPeriod)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_closed' => 'boolean',
        ]);

        $accountingPeriod->update($data);

        return redirect()->route('admin.finance.periods.index')->with('success', 'Periode akuntansi berhasil diperbarui.');
    }

    public function destroy(AccountingPeriod $accountingPeriod)
    {
        try {
            $accountingPeriod->delete();
            return redirect()->route('admin.finance.periods.index')->with('success', 'Periode akuntansi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.finance.periods.index')->with('error', 'Periode akuntansi gagal dihapus.');
        }
    }

    public function close(AccountingPeriod $accountingPeriod)
    {
        $accountingPeriod->update([
            'is_closed' => true,
            'closed_at' => now(),
        ]);

        return redirect()->route('admin.finance.periods.index')->with('success', 'Periode akuntansi berhasil ditutup.');
    }
}
