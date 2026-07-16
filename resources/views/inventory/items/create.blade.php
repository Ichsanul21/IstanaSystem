<x-layouts.admin title="Tambah Item">
    <x-slot:header>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.inventory.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Item</h1>
        </div>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.inventory.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-ui.input name="code" label="Kode Item" value="Auto-generated" disabled />
                <x-ui.input name="name" label="Nama Item" required />
                <x-ui.select name="category" label="Kategori" :options="['chemical' => 'Chemical', 'packaging' => 'Packaging', 'tool' => 'Tool', 'other' => 'Lainnya']" />
                <x-ui.select name="unit" label="Satuan" :options="['pcs' => 'Pcs', 'kg' => 'Kg', 'ltr' => 'Liter']" required />
                <x-ui.input name="min_stock" label="Min. Stok" type="number" min="0" />
            </div>
            <x-ui.textarea name="description" label="Deskripsi" rows="3" />
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked id="is_active" class="rounded border-gray-300 text-primary focus:ring-primary">
                <x-ui.label for="is_active" class="!mb-0">Aktif</x-ui.label>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                <x-ui.button href="{{ route('admin.inventory.index') }}" variant="secondary">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.admin>
