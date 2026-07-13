<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DailyCashFlow;
use App\Models\Workshop;
use Illuminate\Http\Request;

class BranchApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Branch::with('workshop');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return response()->json(['data' => $query->get()->map(fn($b) => [
            'id' => $b->id,
            'code' => $b->code,
            'name' => $b->name,
            'workshop' => $b->workshop ? ['id' => $b->workshop->id, 'name' => $b->workshop->name] : null,
            'address' => $b->address ?? '',
            'phone' => $b->phone ?? '',
            'opening_time' => $b->opening_time ?? '08:00',
            'closing_time' => $b->closing_time ?? '21:00',
            'is_active' => (bool) $b->is_active,
            'daily_capacity' => $b->daily_capacity ?? 50,
        ])]);
    }

    public function show($id)
    {
        $branch = Branch::with('workshop')->findOrFail($id);

        return response()->json(['data' => [
            'id' => $branch->id,
            'code' => $branch->code,
            'name' => $branch->name,
            'workshop' => $branch->workshop ? ['id' => $branch->workshop->id, 'name' => $branch->workshop->name] : null,
            'address' => $branch->address ?? '',
            'phone' => $branch->phone ?? '',
            'opening_time' => $branch->opening_time ?? '08:00',
            'closing_time' => $branch->closing_time ?? '21:00',
            'is_active' => (bool) $branch->is_active,
            'daily_capacity' => $branch->daily_capacity ?? 50,
        ]]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:branches,code',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
            'daily_capacity' => 'nullable|integer|min:1',
        ]);

        $branch = Branch::create($data);

        return response()->json(['success' => true, 'data' => ['id' => $branch->id]], 201);
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $data = $request->validate([
            'code' => "nullable|string|max:20|unique:branches,code,{$id}",
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
            'daily_capacity' => 'nullable|integer|min:1',
        ]);

        $branch->update($data);

        return response()->json(['success' => true, 'message' => 'Branch updated']);
    }

    public function dailyCashFlow(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $query = DailyCashFlow::where('branch_id', $branch->id);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $flows = $query->get();

        return response()->json(['data' => $flows]);
    }

    public function switchBranch($id)
    {
        $branch = Branch::findOrFail($id);

        session(['current_branch_id' => $branch->id]);

        return response()->json(['success' => true, 'data' => ['branch_id' => $branch->id, 'name' => $branch->name]]);
    }

    public function workshops()
    {
        $workshops = Workshop::where('is_active', true)->get();

        return response()->json(['data' => $workshops]);
    }
}
