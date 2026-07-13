<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingApiController extends Controller
{
    public function __construct(protected SettingService $settingService) {}

    public function index()
    {
        $groups = ['general', 'tax', 'loyalty', 'gateway', 'accounting', 'order', 'notification', 'inventory'];
        $settings = [];

        foreach ($groups as $group) {
            $settings[$group] = $this->settingService->getGroup($group);
        }

        return response()->json(['data' => $settings]);
    }

    public function show($group)
    {
        $settings = $this->settingService->getGroup($group);

        return response()->json(['data' => [
            'group' => $group,
            'settings' => $settings,
        ]]);
    }

    public function update(Request $request, $group)
    {
        $data = $request->validate([
            'points_ratio' => 'nullable|integer|min:100',
            'points_redeem_rate' => 'nullable|integer|min:1',
            'points_expiry_days' => 'nullable|integer|min:1',
            'min_order_amount' => 'nullable|numeric|min:0',
            'auto_upgrade' => 'nullable|boolean',
        ]);

        $this->settingService->updateGroup($group, $data);

        return response()->json(['success' => true, 'message' => 'Settings updated']);
    }

    public function branchSettingsIndex()
    {
        $settings = $this->settingService->getGroup('branch', currentBranchId());

        return response()->json(['data' => $settings]);
    }

    public function branchSettingsUpdate(Request $request)
    {
        $data = $request->all();
        $this->settingService->updateGroup('branch', $data, currentBranchId());

        return response()->json(['success' => true, 'message' => 'Branch settings updated']);
    }
}
