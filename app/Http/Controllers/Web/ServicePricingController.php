<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Service;
use App\Models\ServicePricing;
use Illuminate\Http\Request;

class ServicePricingController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Developer,Super Admin,Branch Admin')->only(['index']);
        $this->middleware('role:Developer,Super Admin')->except(['index']);
    }

    public function index()
    {
        $branchId = request('branch_id', currentBranchId());
        $branches = Branch::where('is_active', true)->get();
        $services = Service::where('is_active', true)->get();
        $pricings = ServicePricing::with('service')
            ->where('branch_id', $branchId)
            ->latest()
            ->paginate(20);

        return view('services.pricing', compact('pricings', 'branches', 'branchId', 'services'));
    }

    public function create()
    {
        $branchId = request('branch_id', currentBranchId());
        $branches = Branch::where('is_active', true)->get();
        $services = Service::where('is_active', true)->get();
        $pricings = ServicePricing::with('service')
            ->where('branch_id', $branchId)
            ->latest()
            ->paginate(20);

        return view('services.pricing', compact('pricings', 'branches', 'branchId', 'services'))
            ->with('showForm', true);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'min_weight' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        ServicePricing::create($data);

        return redirect()->route('admin.services.pricing.index', ['branch_id' => $data['branch_id']])
            ->with('success', 'Harga layanan berhasil ditambahkan');
    }

    public function edit(ServicePricing $pricing)
    {
        $branchId = $pricing->branch_id;
        $branches = Branch::where('is_active', true)->get();
        $services = Service::where('is_active', true)->get();
        $pricings = ServicePricing::with('service')
            ->where('branch_id', $branchId)
            ->latest()
            ->paginate(20);

        return view('services.pricing', compact('pricings', 'branches', 'branchId', 'services', 'pricing'))
            ->with('showForm', true);
    }

    public function update(Request $request, ServicePricing $pricing)
    {
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'min_weight' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $pricing->update($data);

        return redirect()->route('admin.services.pricing.index', ['branch_id' => $pricing->branch_id])
            ->with('success', 'Harga layanan berhasil diperbarui');
    }

    public function destroy(ServicePricing $pricing)
    {
        try {
            $branchId = $pricing->branch_id;
            $pricing->delete();

            return redirect()->route('admin.services.pricing.index', ['branch_id' => $branchId])
                ->with('success', 'Harga layanan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.services.pricing.index')
                ->with('error', 'Gagal menghapus harga layanan');
        }
    }
}
