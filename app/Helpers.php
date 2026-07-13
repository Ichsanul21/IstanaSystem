<?php

if (!function_exists('currentBranchId')) {
    function currentBranchId(): ?int
    {
        return session('current_branch_id') ?? auth()->user()?->branch_id;
    }
}

if (!function_exists('formatRupiah')) {
    function formatRupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

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

if (!function_exists('generateOrderNumber')) {
    function generateOrderNumber(string $branchCode): string
    {
        $date = now()->format('Ymd');
        $last = \App\Models\Order::whereDate('created_at', today())
            ->lockForUpdate()
            ->count();
        
        return sprintf('%s-%s-%05d', $branchCode, $date, $last + 1);
    }
}
