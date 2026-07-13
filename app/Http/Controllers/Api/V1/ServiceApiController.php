<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServicePricing;
use Illuminate\Http\Request;

class ServiceApiController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)->get();

        return response()->json(['data' => $services->map(fn($s) => [
            'id' => $s->id,
            'code' => $s->code,
            'name' => $s->name,
            'unit' => $s->unit ?? 'kg',
            'description' => $s->description ?? '',
            'is_active' => (bool) $s->is_active,
        ])]);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);

        return response()->json(['data' => [
            'id' => $service->id,
            'code' => $service->code,
            'name' => $service->name,
            'unit' => $service->unit ?? 'kg',
            'description' => $service->description ?? '',
            'is_active' => (bool) $service->is_active,
        ]]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:services,code',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $service = Service::create($data);

        return response()->json(['success' => true, 'data' => ['id' => $service->id]], 201);
    }

    public function pricings(Request $request)
    {
        $query = ServicePricing::with('service');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        return response()->json(['data' => $query->get()->map(fn($p) => [
            'id' => $p->id,
            'service_id' => $p->service_id,
            'service' => $p->service ? ['code' => $p->service->code, 'name' => $p->service->name] : null,
            'branch_id' => $p->branch_id,
            'price' => (float) $p->price,
            'min_weight' => (float) ($p->min_weight ?? 0),
            'is_active' => (bool) $p->is_active,
        ])]);
    }

    public function updatePricing(Request $request, $id)
    {
        $pricing = ServicePricing::findOrFail($id);

        $data = $request->validate([
            'price' => 'required|numeric|min:0',
            'min_weight' => 'nullable|numeric|min:0',
        ]);

        $pricing->update($data);

        return response()->json(['success' => true, 'message' => 'Pricing updated']);
    }

    public function bulkUpdatePricing(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'pricings' => 'required|array',
            'pricings.*.service_id' => 'required|exists:services,id',
            'pricings.*.price' => 'required|numeric|min:0',
        ]);

        foreach ($request->pricings as $item) {
            ServicePricing::updateOrCreate(
                ['service_id' => $item['service_id'], 'branch_id' => $request->branch_id],
                ['price' => $item['price']]
            );
        }

        return response()->json(['success' => true, 'message' => 'Pricings updated']);
    }
}
