<?php

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return app(\App\Services\SettingService::class)->get($key, $default);
    }
}

if (!function_exists('branchSetting')) {
    function branchSetting(string $key, mixed $default = null, ?int $branchId = null): mixed
    {
        return app(\App\Services\SettingService::class)->getBranch($key, $branchId, $default);
    }
}
