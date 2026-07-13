<x-layouts.admin title="Stock Out — {{ $item->name }}">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.inventory.show', $item) }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Out — {{ $item->name }}</h1>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <x-ui.card>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Informasi Stok</p>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nama Item</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Stok</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($item->batches->sum('quantity')) }} {{ $item->unit }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Batch Tersedia</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->batches->count() }}</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="mt-6">
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Batch Stok</h3>
                </x-slot:header>
                <div class="space-y-2">
                    @foreach($item->batches as $batch)
                        @php $remaining = $batch->quantity - $batch->transactions->sum('quantity'); @endphp
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700 dark:text-gray-300 font-mono">{{ $batch->batch_code }}</span>
                            <span class="font-medium {{ $remaining <= 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">{{ number_format($remaining) }} {{ $item->unit }}</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        </div>

        <div class="lg:col-span-2">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Form Stock Out</h3>
                </x-slot:header>
                <form method="POST" action="{{ route('admin.inventory.stock.deduct', $item) }}" class="space-y-4">
                    @csrf
                    <x-ui.input name="quantity" label="Jumlah" type="number" step="0.01" min="0.01" required help="Jumlah stok yang akan dikeluarkan" />
                    <x-ui.textarea name="notes" label="Catatan" rows="3" help="Alasan pengeluaran stok (opsional)" />
                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <x-ui.button href="{{ route('admin.inventory.show', $item) }}" variant="ghost">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary">Keluarkan Stok</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-layouts.admin>
