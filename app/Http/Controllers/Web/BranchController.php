<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Workshop;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with('workshop')->latest()->paginate(15);

        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        $workshops = Workshop::where('is_active', true)->get();

        return view('branches.create', compact('workshops'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:branches,code',
            'name' => 'required|string|max:255',
            'workshop_id' => 'nullable|exists:workshops,id',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'daily_capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        Branch::create($data);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Cabang berhasil ditambahkan');
    }

    public function show(Branch $branch)
    {
        $branch->load('workshop', 'users');

        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $workshops = Workshop::where('is_active', true)->get();

        return view('branches.edit', compact('branch', 'workshops'));
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:branches,code,' . $branch->id,
            'name' => 'required|string|max:255',
            'workshop_id' => 'nullable|exists:workshops,id',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'daily_capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $branch->update($data);

        return redirect()->route('admin.branches.show', $branch)
            ->with('success', 'Cabang berhasil diperbarui');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('admin.branches.index')
            ->with('success', 'Cabang berhasil dihapus');
    }

    public function switch(Branch $branch)
    {
        session(['current_branch_id' => $branch->id]);

        return redirect()->route('admin.dashboard');
    }
}
