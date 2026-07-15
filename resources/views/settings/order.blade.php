<x-settings.group-layout title="Pesanan" description="Pengaturan order dan alur kerja" group="order">
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Nomor Pesanan</h3>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.input
                name="prefix_pattern"
                label="Pola Prefix Pesanan"
                value="{{ old('prefix_pattern', $settingValues['prefix_pattern'] ?? 'ORD-') }}"
                help="Contoh: ORD-, LAUN-"
            />
        </div>
    </x-ui.card>

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Alur Pesanan</h3>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.select
                name="auto_confirm"
                label="Konfirmasi Otomatis"
                :options="['1' => 'Ya', '0' => 'Tidak']"
                value="{{ old('auto_confirm', $settingValues['auto_confirm'] ?? '0') }}"
                help="Konfirmasi order baru secara otomatis"
            />
            @php $defaultStatusOptions = collect(\App\Enums\OrderStatus::cases())->reject(fn($s) => $s->isTerminal())->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray(); @endphp
            <x-ui.select
                name="default_status"
                label="Status Default Order Baru"
                :options="$defaultStatusOptions"
                value="{{ old('default_status', $settingValues['default_status'] ?? 'pending') }}"
            />
        </div>
    </x-ui.card>
</x-settings.group-layout>
