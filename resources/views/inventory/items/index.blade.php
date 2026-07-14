<x-layouts.admin title="Inventaris">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventaris</h1>
            @can('inventory.create')
            <x-ui.button href="{{ route('admin.inventory.create') }}" variant="primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Item
            </x-ui.button>
            @endcan
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <x-ui.card>
            <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="w-64">
                    <x-ui.input name="search" label="Cari" placeholder="Nama item..." :value="request('search')" />
                </div>
                <div class="w-48">
                    <x-ui.select name="category" label="Kategori" :options="['chemical' => 'Chemical', 'packaging' => 'Packaging', 'tool' => 'Tool', 'other' => 'Lainnya']" placeholder="Semua Kategori" />
                </div>
                <label class="flex items-center gap-2 mb-1">
                    <input type="checkbox" name="low_stock" value="1" @checked(request('low_stock')) class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Low Stock</span>
                </label>
                <x-ui.button type="submit" variant="primary" size="md">Filter</x-ui.button>
                @if(request()->anyFilled(['search', 'category', 'low_stock']))
                    <x-ui.button href="{{ route('admin.inventory.index') }}" variant="ghost" size="md">Reset</x-ui.button>
                @endif
            </form>
        </x-ui.card>

        <x-ui.card padding="none">
            <x-ui.table :headers="[
                ['label' => 'Nama Item', 'sortable' => true],
                ['label' => 'Kategori'],
                ['label' => 'Total Stok'],
                ['label' => 'Satuan'],
                ['label' => 'Alert Level'],
                ['label' => 'Status'],
                ['label' => 'Aksi'],
            ]">
                @php $totalStock = fn($i) => $i->batches->sum('quantity'); @endphp
                @forelse($items as $item)
                    @php $stock = $totalStock($item); @endphp
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            <a href="{{ route('admin.inventory.show', $item) }}" class="text-primary hover:underline">{{ $item->name }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $item->category ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ number_format($stock) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $item->unit }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $item->min_stock ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock <= 0)
                                <x-ui.badge variant="danger">Habis</x-ui.badge>
                            @elseif($stock <= $item->min_stock)
                                <x-ui.badge variant="warning">Low</x-ui.badge>
                            @else
                                <x-ui.badge variant="success">In Stock</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <x-ui.button href="{{ route('admin.inventory.show', $item) }}" variant="ghost" size="sm">Detail</x-ui.button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada item ditemukan.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        @if($items->hasPages())
            <x-ui.pagination :paginator="$items" />
        @endif
    </div>
</x-layouts.admin>
