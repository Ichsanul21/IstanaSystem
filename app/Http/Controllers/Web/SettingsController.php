<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(
        protected SettingService $settings
    ) {}

    public function index()
    {
        return view('settings.index');
    }

    public function group(string $group)
    {
        $settingValues = $this->settings->getGroup($group);

        $data = ['group' => $group, 'settingValues' => $settingValues];

        return match ($group) {
            'general' => view('settings.general', $data),
            'tax' => view('settings.tax', $data),
            'loyalty' => view('settings.loyalty', $data),
            'gateway' => view('settings.gateway', $data),
            'accounting' => view('settings.accounting', $data),
            'order' => view('settings.order', $data),
            'notification' => view('settings.notification', $data),
            'inventory' => view('settings.inventory', $data),
            default => redirect()->route('admin.settings.index'),
        };
    }

    public function updateGroup(string $group, Request $request)
    {
        $data = $request->except('_token');
        $this->settings->updateGroup($group, $data);

        return redirect()->route('admin.settings.group', $group)
            ->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
