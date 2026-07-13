@php
    $periods = \App\Models\AccountingPeriod::orderByDesc('start_date')->get();
    $accounts = \App\Models\ChartOfAccount::where('is_active', true)->orderBy('code')->get();
    $accountOptions = $accounts->pluck('name', 'code')->mapWithKeys(fn ($name, $code) => [$code => "{$code} - {$name}"])->toArray();
@endphp

<x-settings::group-layout title="Akuntansi" description="Pengaturan akuntansi dan keuangan" group="accounting">
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Periode Aktif</h3>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.select
                name="active_period_id"
                label="Periode Akuntansi Aktif"
                :options="$periods->pluck('name', 'id')->toArray()"
                placeholder="Pilih periode"
                value="{{ old('active_period_id', $settingValues['active_period_id'] ?? '') }}"
            />
        </div>
    </x-ui.card>

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Akun Default</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Akun default untuk transaksi otomatis</p>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.select
                name="default_revenue_acct"
                label="Akun Pendapatan"
                :options="$accountOptions"
                placeholder="Pilih akun"
                value="{{ old('default_revenue_acct', $settingValues['default_revenue_acct'] ?? '') }}"
            />
            <x-ui.select
                name="default_expense_acct"
                label="Akun Beban"
                :options="$accountOptions"
                placeholder="Pilih akun"
                value="{{ old('default_expense_acct', $settingValues['default_expense_acct'] ?? '') }}"
            />
        </div>
    </x-ui.card>
</x-settings::group-layout>
