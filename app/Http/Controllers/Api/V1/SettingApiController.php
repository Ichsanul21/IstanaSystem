<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        return ApiResponse::success($settings);
    }

    public function show($group)
    {
        $settings = $this->settingService->getGroup($group);

        return ApiResponse::success([
            'group' => $group,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request, $group)
    {
        $allowedGroups = ['general', 'tax', 'loyalty', 'gateway', 'accounting', 'order', 'notification', 'inventory'];

        if (!in_array($group, $allowedGroups)) {
            return ApiResponse::error('Invalid settings group', null, 422);
        }

        $rules = $this->getValidationRules($group);

        $data = $request->validate($rules);

        $this->settingService->updateGroup($group, $data);

        return ApiResponse::success(null, 'Settings updated');
    }

    public function branchSettingsIndex()
    {
        $settings = $this->settingService->getGroup('branch', currentBranchId());

        return ApiResponse::success($settings);
    }

    public function branchSettingsUpdate(Request $request)
    {
        $data = $request->validate([
            'printer_name' => 'nullable|string|max:100',
            'receipt_footer' => 'nullable|string|max:500',
            'auto_receipt_print' => 'nullable|boolean',
        ]);

        $this->settingService->updateGroup('branch', $data, currentBranchId());

        return ApiResponse::success(null, 'Branch settings updated');
    }

    private function getValidationRules(string $group): array
    {
        return match ($group) {
            'general' => [
                'store_name' => 'nullable|string|max:255',
                'store_address' => 'nullable|string|max:500',
                'store_phone' => 'nullable|string|max:20',
                'store_email' => 'nullable|email|max:255',
                'currency' => 'nullable|string|max:10',
            ],
            'tax' => [
                'tax_rate' => 'nullable|numeric|min:0|max:100',
                'tax_regime' => ['nullable', Rule::in(['pp23', 'ppn'])],
                'ppn_percentage' => 'nullable|numeric|min:0|max:100',
            ],
            'loyalty' => [
                'points_ratio' => 'nullable|integer|min:100',
                'points_redeem_rate' => 'nullable|integer|min:1',
                'points_expiry_days' => 'nullable|integer|min:1',
                'min_order_amount' => 'nullable|numeric|min:0',
                'auto_upgrade' => 'nullable|boolean',
            ],
            'gateway' => [
                'midtrans_server_key' => 'nullable|string|max:255',
                'midtrans_client_key' => 'nullable|string|max:255',
                'midtrans_is_production' => 'nullable|boolean',
            ],
            'accounting' => [
                'coa_prefix' => 'nullable|string|max:10',
                'fiscal_year_start' => 'nullable|string|max:10',
            ],
            'order' => [
                'min_order_amount' => 'nullable|numeric|min:0',
                'auto_upgrade' => 'nullable|boolean',
            ],
            'notification' => [
                'wa_gateway' => ['nullable', Rule::in(['fonnte', 'wablas', 'whatsmeow'])],
                'wa_api_key' => 'nullable|string|max:255',
                'wa_phone_number' => 'nullable|string|max:20',
            ],
            'inventory' => [
                'low_stock_threshold' => 'nullable|numeric|min:0',
                'auto_order' => 'nullable|boolean',
            ],
            default => ['*' => 'nullable'],
        };
    }
}
