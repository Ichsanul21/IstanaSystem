<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GatewayConfiguration;
use Illuminate\Http\Request;

class GatewayConfigurationController extends Controller
{
    public function index()
    {
        $config = GatewayConfiguration::first();
        $settingValues = $config ? $config->toArray() : [];

        return view('settings.payment-gateway', compact('settingValues'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'provider' => 'nullable|string|max:50',
            'merchant_id' => 'nullable|string|max:100',
            'client_key' => 'nullable|string|max:255',
            'server_key' => 'nullable|string|max:255',
            'is_production' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $config = GatewayConfiguration::first();

        if ($config) {
            $config->update($data);
        } else {
            GatewayConfiguration::create($data);
        }

        return redirect()->route('admin.settings.group', 'gateway')->with('success', 'Konfigurasi gateway berhasil diperbarui.');
    }
}
