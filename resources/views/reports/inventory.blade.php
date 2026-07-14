<x-layouts.admin title="Laporan Inventory">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Inventory</h1>
            @can('report.export')
            <x-ui.button href="#" variant="primary" size="sm">Export Excel</x-ui.button>
            @endcan
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Item</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $items->total() }}</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nilai Stok</p>
            <p class="text-2xl font-bold text-primary mt-1">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stok Menipis</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $lowStock }}</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stok Habis</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $outOfStock }}</p>
        </x-ui.card>
    </div>

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Semua Item Inventory</h3>
        </x-slot:header>
        <x-ui.table :headers="['Kode', 'Nama Item', 'Kategori', 'Stok', 'Satuan', 'Status']">
            @forelse($items as $item)
            @php $qty = $item->batches->sum('quantity'); @endphp
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $item->code }}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->category ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $qty }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->unit }}</td>
                <td class="px-6 py-4">
                    <x-ui.badge :variant="$qty <= 0 ? 'danger' : ($qty < 10 ? 'warning' : 'success')" size="sm">{{ $qty <= 0 ? 'Habis' : ($qty < 10 ? 'Menipis' : 'Aman') }}</x-ui.badge>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada data inventory.</td>
            </tr>
            @endforelse
        </x-ui.table>
        @if(method_exists($items, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $items->links() }}
            </div>
        @endif
    </x-ui.card>
</x-layouts.admin>
