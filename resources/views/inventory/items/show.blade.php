<x-layouts.admin title="{{ $item->name }}">
    @php $totalStock = $item->batches->sum('quantity'); @endphp
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.inventory.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $item->name }}</h1>
                @if($totalStock <= 0)
                    <x-ui.badge variant="danger">Habis</x-ui.badge>
                @elseif($totalStock <= $item->min_stock)
                    <x-ui.badge variant="warning">Low</x-ui.badge>
                @else
                    <x-ui.badge variant="success">In Stock</x-ui.badge>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @can('inventory.update')
                <x-ui.button href="{{ route('admin.inventory.edit', $item) }}" variant="outline">Edit</x-ui.button>
                @endcan
                @can('stock_in')
                <x-ui.button x-on:click="$dispatch('open-modal', 'add-stock-modal')" variant="primary">Tambah Stok</x-ui.button>
                @endcan
                @can('stock_out')
                <x-ui.button x-on:click="$dispatch('open-modal', 'transfer-modal')" variant="secondary">Transfer</x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <x-ui.card>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Informasi Item</p>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kode</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $item->code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Satuan</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->unit }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Min. Stok</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->min_stock ?? '-' }}</p>
                            </div>
                            <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Stok</p>
                                <p class="text-2xl font-bold text-primary">{{ number_format($totalStock) }} <span class="text-sm font-medium text-gray-500">{{ $item->unit }}</span></p>
                            </div>
                        </div>
                    </div>
                </x-ui.card>
            </div>

            <div class="lg:col-span-2">
                <x-ui.card>
                    <x-slot:header>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Batch Stok (FIFO)</h3>
                    </x-slot:header>
                    <x-ui.table :headers="[
                        ['label' => 'Kode Batch'],
                        ['label' => 'Jumlah'],
                        ['label' => 'Harga Beli'],
                        ['label' => 'Tanggal Kadaluarsa'],
                        ['label' => 'Sisa'],
                    ]">
                        @forelse($item->batches as $batch)
                            @php $used = $batch->transactions->sum('quantity'); $remaining = $batch->quantity - $used; @endphp
                            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-mono font-medium text-gray-900 dark:text-white">{{ $batch->batch_code }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ number_format($batch->quantity) }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">Rp {{ number_format($batch->unit_cost) }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $batch->expires_at ? $batch->expires_at->format('d/m/Y') : '-' }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium {{ $remaining <= 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">{{ number_format($remaining) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada stok.</td>
                            </tr>
                        @endforelse
                    </x-ui.table>
                </x-ui.card>
            </div>
        </div>
    </div>

    <x-ui.modal name="add-stock-modal" title="Tambah Stok" maxWidth="md">
        <form method="POST" action="{{ route('admin.inventory.add-stock', $item) }}" class="space-y-4">
            @csrf
            <x-ui.input name="quantity" label="Jumlah" type="number" min="1" required />
            <x-ui.input name="unit_cost" label="Harga Beli" type="number" step="0.01" required />
            <x-ui.input name="expiry_date" label="Tanggal Kadaluarsa (opsional)" type="date" />
            <x-ui.textarea name="notes" label="Catatan (opsional)" rows="2" />
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal name="transfer-modal" title="Transfer Stok" maxWidth="md">
        <form method="POST" action="{{ route('admin.inventory.transfer', $item) }}" class="space-y-4">
            @csrf
            <input type="hidden" name="from_branch_id" value="{{ currentBranchId() }}">
            <x-ui.select name="to_branch_id" label="Cabang Tujuan" :options="$branches" placeholder="Pilih Cabang" required />
            <x-ui.input name="quantity" label="Jumlah" type="number" min="1" required />
            <x-ui.textarea name="notes" label="Catatan (opsional)" rows="2" />
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                <x-ui.button type="submit" variant="primary">Transfer</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</x-layouts.admin>
