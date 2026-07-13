<x-settings::group-layout title="Payment Gateway" description="Pengaturan Midtrans payment gateway" group="gateway">
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Status Gateway</h3>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.select
                name="is_active"
                label="Aktifkan Payment Gateway"
                :options="['1' => 'Aktif', '0' => 'Nonaktif']"
                value="{{ old('is_active', $settingValues['is_active'] ?? '0') }}"
            />
            <x-ui.select
                name="is_production"
                label="Mode"
                :options="['0' => 'Sandbox (Testing)', '1' => 'Produksi']"
                value="{{ old('is_production', $settingValues['is_production'] ?? '0') }}"
                help="Gunakan Sandbox untuk testing"
            />
        </div>
    </x-ui.card>

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Kredensial Midtrans</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Masukkan kredensial dari dashboard Midtrans</p>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.input
                name="client_key"
                label="Client Key"
                type="password"
                value="{{ old('client_key', $settingValues['client_key'] ?? '') }}"
            />
            <x-ui.input
                name="server_key"
                label="Server Key"
                type="password"
                value="{{ old('server_key', $settingValues['server_key'] ?? '') }}"
            />
            <x-ui.input
                name="merchant_id"
                label="Merchant ID"
                value="{{ old('merchant_id', $settingValues['merchant_id'] ?? '') }}"
            />
        </div>
    </x-ui.card>
</x-settings::group-layout>
