<x-layouts.admin title="Riwayat Stok">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Riwayat Stok</h1>
            <x-ui.button href="{{ route('admin.inventory.stock.create') }}" variant="primary">+ Tambah Stok</x-ui.button>
        </div>
    </x-slot:header>

    <x-ui.card>
        <x-ui.table>
            <thead>
                <tr>
                    <x-ui.th>Item</x-ui.th>
                    <x-ui.th>Jumlah</x-ui.th>
                    <x-ui.th>Harga Satuan</x-ui.th>
                    <x-ui.th>Batch</x-ui.th>
                    <x-ui.th>Tanggal</x-ui.th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $stock->item->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">{{ $stock->quantity }}</td>
                        <td class="px-4 py-3 text-sm">Rp {{ number_format($stock->unit_price ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">{{ $stock->batch_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">{{ $stock->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada riwayat stok.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>

        @if($stocks->hasPages())
            <div class="mt-4">
                <x-ui.pagination :paginator="$stocks" />
            </div>
        @endif
    </x-ui.card>
</x-layouts.admin>
