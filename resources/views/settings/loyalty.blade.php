<x-settings.group-layout title="Loyalitas" description="Pengaturan program loyalitas pelanggan" group="loyalty">
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Poin Loyalitas</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Konfigurasi perolehan dan penukaran poin</p>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.input
                name="points_ratio"
                label="Rasio Poin (Rp per 1 Poin)"
                type="number"
                value="{{ old('points_ratio', $settingValues['points_ratio'] ?? '1000') }}"
                help="Contoh: 1000 = dapat 1 poin per Rp 1.000 transaksi"
            />
            <x-ui.input
                name="points_redeem_rate"
                label="Nilai Tukar Poin (Rp per Poin)"
                type="number"
                value="{{ old('points_redeem_rate', $settingValues['redeem_rate'] ?? '100') }}"
                help="Contoh: 100 = 1 poin bernilai Rp 100"
            />
            <x-ui.input
                name="points_expiry_days"
                label="Masa Berlaku Poin (Hari)"
                type="number"
                value="{{ old('points_expiry_days', $settingValues['points_expiry_days'] ?? '90') }}"
            />
            <x-ui.input
                name="min_order_amount"
                label="Minimum Transaksi untuk Poin (Rp)"
                type="number"
                value="{{ old('min_order_amount', $settingValues['min_order_amount'] ?? '50000') }}"
            />
        </div>
    </x-ui.card>

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Otomatisasi</h3>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.select
                name="auto_upgrade"
                label="Auto Upgrade Level Pelanggan"
                :options="['1' => 'Aktif', '0' => 'Nonaktif']"
                value="{{ old('auto_upgrade', $settingValues['auto_upgrade'] ?? '1') }}"
                help="Otomatis upgrade level pelanggan berdasarkan total transaksi"
            />
        </div>
    </x-ui.card>
</x-settings.group-layout>
