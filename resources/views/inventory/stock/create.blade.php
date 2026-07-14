<x-layouts.admin title="Tambah Stok">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Stok</h1>
            <x-ui.button href="{{ route('admin.inventory.stock.index') }}" variant="ghost">Kembali</x-ui.button>
        </div>
    </x-slot:header>

    <div class="max-w-2xl mx-auto">
        <x-ui.card>
            <form method="POST" action="{{ route('admin.inventory.stock.store') }}" class="space-y-4">
                @csrf

                <x-ui.select name="inventory_item_id" label="Item" :options="$items->map(fn($i) => ['value' => $i->id, 'label' => $i->name])->toArray()" required />

                <x-ui.input name="quantity" label="Jumlah" type="number" step="0.01" min="0" required />

                <x-ui.input name="unit_price" label="Harga Satuan" type="number" step="1" min="0" />

                <x-ui.input name="batch_number" label="Nomor Batch" />

                <x-ui.input name="expired_at" label="Tanggal Kadaluarsa" type="date" />

                <x-ui.textarea name="notes" label="Catatan" />

                <div class="flex justify-end space-x-3">
                    <x-ui.button type="button" variant="ghost" onclick="window.location='{{ route('admin.inventory.stock.index') }}'">Batal</x-ui.button>
                    <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.admin>
