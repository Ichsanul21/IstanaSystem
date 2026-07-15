<x-layouts.admin title="Pengaturan Cabang">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pengaturan Cabang</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pengaturan khusus untuk setiap cabang</p>
            </div>
            <div class="flex items-center gap-3">
                <select x-on:change="window.location.href = '/admin/settings/branches/' + $event.target.value" class="block w-44 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm px-3 py-2 text-sm">
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" @selected($b->id === $branch->id)>{{ $b->name }}</option>
                    @endforeach
                </select>
                <x-ui.button type="submit" form="branch-settings-form" variant="primary">Simpan</x-ui.button>
            </div>
        </div>
    </x-slot:header>

    <form id="branch-settings-form" method="POST" action="{{ route('admin.branch-settings.update', $branch) }}" class="space-y-6">
        @csrf

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Cabang</h3>
            </x-slot:header>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="branch_name" label="Nama Cabang" value="{{ $branch->name }}" disabled />
                <x-ui.input name="branch_code" label="Kode Cabang" value="{{ $branch->code }}" disabled />
                <x-ui.input name="branch_address" label="Alamat" value="{{ old('branch_address', $settingValues['branch_address'] ?? $branch->address) }}" />
                <x-ui.input name="branch_phone" label="Telepon" value="{{ old('branch_phone', $settingValues['branch_phone'] ?? $branch->phone) }}" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Operasional</h3>
            </x-slot:header>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="branch_open_time" label="Jam Buka" type="time" value="{{ old('branch_open_time', $settingValues['branch_open_time'] ?? $branch->opening_time ?? '08:00') }}" />
                <x-ui.input name="branch_close_time" label="Jam Tutup" type="time" value="{{ old('branch_close_time', $settingValues['branch_close_time'] ?? $branch->closing_time ?? '21:00') }}" />
                <x-ui.select name="branch_operational_days" label="Hari Operasional" :options="['all' => 'Senin - Minggu', 'weekdays' => 'Senin - Sabtu', 'custom' => 'Kustom']" value="{{ old('branch_operational_days', $settingValues['branch_operational_days'] ?? 'all') }}" />
                <x-ui.input name="branch_max_capacity" label="Kapasitas Harian" type="number" value="{{ old('branch_max_capacity', $settingValues['branch_max_capacity'] ?? $branch->daily_capacity ?? '100') }}" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Harga & Layanan</h3>
            </x-slot:header>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="branch_price_regular" label="Harga Reguler (/kg)" type="number" value="{{ old('branch_price_regular', $settingValues['branch_price_regular'] ?? '8000') }}" />
                <x-ui.input name="branch_price_express" label="Harga Express (/kg)" type="number" value="{{ old('branch_price_express', $settingValues['branch_price_express'] ?? '15000') }}" />
                <x-ui.input name="branch_price_dry_clean" label="Harga Dry Cleaning" type="number" value="{{ old('branch_price_dry_clean', $settingValues['branch_price_dry_clean'] ?? '25000') }}" />
                <x-ui.input name="branch_min_order" label="Min. Order (Rp)" type="number" value="{{ old('branch_min_order', $settingValues['branch_min_order'] ?? '20000') }}" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pajak Cabang</h3>
            </x-slot:header>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="branch_tax_rate" label="Tarif Pajak (%)" type="number" value="{{ old('branch_tax_rate', $settingValues['branch_tax_rate'] ?? '11') }}" />
                <x-ui.select name="branch_tax_regime" label="Rezim Pajak" :options="['inclusive' => 'Inklusif', 'exclusive' => 'Eksklusif']" value="{{ old('branch_tax_regime', $settingValues['branch_tax_regime'] ?? 'exclusive') }}" />
            </div>
        </x-ui.card>

        <div class="flex items-center justify-end gap-3">
            <x-ui.button type="reset" variant="ghost">Reset</x-ui.button>
            <x-ui.button type="submit" variant="primary">Simpan Pengaturan</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
