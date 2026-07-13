<x-settings::group-layout title="Inventaris" description="Pengaturan manajemen stok dan inventaris" group="inventory">
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pengaturan Stok</h3>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.input
                name="default_min_stock"
                label="Batas Stok Minimum Default"
                type="number"
                value="{{ old('default_min_stock', $settingValues['default_min_stock'] ?? '10') }}"
                help="Notifikasi saat stok di bawah angka ini"
            />
            <x-ui.select
                name="enable_fifo"
                label="Metode FIFO"
                :options="['1' => 'Aktif', '0' => 'Nonaktif']"
                value="{{ old('enable_fifo', $settingValues['enable_fifo'] ?? '1') }}"
                help="First In First Out untuk pengeluaran stok"
            />
        </div>
    </x-ui.card>
</x-settings::group-layout>
