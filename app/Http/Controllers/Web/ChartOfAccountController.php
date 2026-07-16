<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function index()
    {
        $accounts = ChartOfAccount::latest()->paginate(15);

        return view('finance.coa.index', compact('accounts'));
    }

    public function create()
    {
        return view('finance.coa.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:chart_of_accounts,code',
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:asset,liability,equity,revenue,expense',
            'normal_balance' => 'nullable|string|in:debit,credit',
            'is_active' => 'boolean',
        ]);

        ChartOfAccount::create($data);

        return redirect()->route('admin.finance.accounts')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(ChartOfAccount $chartOfAccount)
    {
        return view('finance.coa.edit', ['account' => $chartOfAccount]);
    }

    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:chart_of_accounts,code,' . $chartOfAccount->id,
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:asset,liability,equity,revenue,expense',
            'normal_balance' => 'nullable|string|in:debit,credit',
            'is_active' => 'boolean',
        ]);

        $chartOfAccount->update($data);

        return redirect()->route('admin.finance.accounts')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(ChartOfAccount $chartOfAccount)
    {
        try {
            $chartOfAccount->delete();
            return redirect()->route('admin.finance.accounts')->with('success', 'Akun berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.finance.accounts')->with('error', 'Akun gagal dihapus.');
        }
    }
}
