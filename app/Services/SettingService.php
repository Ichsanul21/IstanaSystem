<?php

namespace App\Services;

use App\Models\BranchSetting;
use App\Models\Setting;

class SettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting || is_null($setting->value)) {
            return $default;
        }

        return $setting->value;
    }

    public function set(string $key, mixed $value, string $type = 'string', ?string $group = null, ?string $description = null): Setting
    {
        $attributes = ['key' => $key];

        $values = ['value' => $value];
        if ($group !== null) {
            $values['group'] = $group;
        }
        if ($type !== null) {
            $values['type'] = $type;
        }
        if ($description !== null) {
            $values['description'] = $description;
        }

        return Setting::updateOrCreate($attributes, $values);
    }

    public function getBranch(string $key, ?int $branchId = null, mixed $default = null): mixed
    {
        $branchId = $branchId ?? currentBranchId();

        if (!$branchId) {
            return $this->get($key, $default);
        }

        $setting = BranchSetting::where('branch_id', $branchId)
            ->where('key', $key)
            ->first();

        if (!$setting || is_null($setting->value)) {
            return $this->get($key, $default);
        }

        return $setting->value;
    }

    public function setBranch(int $branchId, string $key, mixed $value, string $type = 'string', ?string $group = null): BranchSetting
    {
        $attributes = ['branch_id' => $branchId, 'key' => $key];

        $values = ['value' => $value];
        if ($group !== null) {
            $values['group'] = $group;
        }
        if ($type !== null) {
            $values['type'] = $type;
        }

        return BranchSetting::updateOrCreate($attributes, $values);
    }

    public function getGroup(string $group, ?int $branchId = null): array
    {
        if ($branchId) {
            return BranchSetting::where('branch_id', $branchId)
                ->where('group', $group)
                ->pluck('value', 'key')
                ->toArray();
        }

        return Setting::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    public function updateGroup(string $group, array $data, ?int $branchId = null): void
    {
        foreach ($data as $key => $value) {
            if ($branchId) {
                BranchSetting::updateOrCreate(
                    ['branch_id' => $branchId, 'key' => $key],
                    ['value' => $value, 'group' => $group]
                );
            } else {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group' => $group]
                );
            }
        }
    }
}
