<x-layouts.admin title="Tambah Tier Member">
    <x-slot:header>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.membership-tiers.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Tier Member</h1>
        </div>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.membership-tiers.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-ui.input name="name" label="Nama Tier" required value="{{ old('name') }}" />
                <x-ui.input name="min_points" label="Minimum Poin" type="number" required value="{{ old('min_points', 0) }}" />
                <x-ui.input name="discount_percent" label="Diskon (%)" type="number" step="0.01" required value="{{ old('discount_percent', 0) }}" />
                <x-ui.input name="color" label="Warna (Hex)" placeholder="#FFFFFF" value="{{ old('color') }}" />
                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktif</label>
                </div>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                <x-ui.button href="{{ route('admin.membership-tiers.index') }}" variant="secondary">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.admin>
