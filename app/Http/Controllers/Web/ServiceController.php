<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::when(request('search'), function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        })->latest()->paginate(15);

        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:services,code',
            'name' => 'required|string|max:100',
            'unit' => 'required|in:kg,pcs,m2',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Service::create($data);

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan berhasil ditambahkan');
    }

    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:services,code,' . $service->id,
            'name' => 'required|string|max:100',
            'unit' => 'required|in:kg,pcs,m2',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $service->update($data);

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan berhasil diperbarui');
    }

    public function destroy(Service $service)
    {
        try {
            if ($service->servicePricings()->exists()) {
                return redirect()->route('admin.services.index')
                    ->with('error', 'Layanan tidak dapat dihapus karena masih memiliki harga cabang');
            }

            $service->delete();

            return redirect()->route('admin.services.index')
                ->with('success', 'Layanan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.services.index')
                ->with('error', 'Gagal menghapus layanan');
        }
    }
}
