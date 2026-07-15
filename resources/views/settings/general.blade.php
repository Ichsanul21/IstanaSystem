<x-settings.group-layout title="Umum" description="Pengaturan umum aplikasi" group="general">
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Toko</h3>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.input
                name="store_name"
                label="Nama Toko"
                value="{{ old('store_name', $settingValues['store_name'] ?? 'Istana Laundry') }}"
            />
            <x-ui.select
                name="timezone"
                label="Zona Waktu"
                :options="['Asia/Jakarta' => 'WIB (Jakarta)', 'Asia/Makassar' => 'WITA (Makassar)', 'Asia/Jayapura' => 'WIT (Jayapura)']"
                value="{{ old('timezone', $settingValues['timezone'] ?? 'Asia/Jakarta') }}"
            />
            <x-ui.select
                name="currency"
                label="Mata Uang"
                :options="['IDR' => 'IDR - Rupiah Indonesia', 'USD' => 'USD - US Dollar']"
                value="{{ old('currency', $settingValues['currency'] ?? 'IDR') }}"
            />
        </div>
    </x-ui.card>

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Logo</h3>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                @if (!empty($settingValues['logo']))
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $settingValues['logo']) }}" alt="Logo" class="h-20 w-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    </div>
                @endif
                <x-ui.label for="logo">Unggah Logo</x-ui.label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: PNG, JPG, SVG. Maks 2MB.</p>
                <input
                    type="file"
                    name="logo"
                    id="logo"
                    accept="image/*"
                    class="mt-2 block w-full text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary file:text-white hover:file:bg-primary-dark"
                />
            </div>
        </div>
    </x-ui.card>
</x-settings.group-layout>
