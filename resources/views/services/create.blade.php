<x-layouts.admin title="Tambah Layanan">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Layanan</h1>
            <x-ui.button href="{{ route('admin.services.index') }}" variant="ghost">Kembali</x-ui.button>
        </div>
    </x-slot:header>

    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.services.store') }}">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input name="code" label="Kode Layanan" required placeholder="Contoh: CK" />
                    <x-ui.input name="name" label="Nama Layanan" required placeholder="Contoh: Cuci Kering" />
                </div>
                <x-ui.select name="unit" label="Satuan" :options="['kg' => 'Kg', 'pcs' => 'Pcs', 'm2' => 'M2']" placeholder="Pilih Satuan" required />
                <x-ui.textarea name="description" label="Deskripsi" rows="3" placeholder="Deskripsi layanan (opsional)" />
                <div>
                    <x-ui.label for="is_active">Status</x-ui.label>
                    <div class="mt-1 flex items-center gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="is_active" value="1" checked class="text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="is_active" value="0" class="text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Nonaktif</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-ui.button type="button" variant="ghost" onclick="window.location='{{ route('admin.services.index') }}'">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.admin>
