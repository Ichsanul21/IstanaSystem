<x-layouts.admin title="Transfer Stok">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.inventory.show', $item) }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Transfer Stok — {{ $item->name }}</h1>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <x-ui.card>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Informasi Item</p>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nama Item</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kode</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $item->code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Stok Tersedia</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($item->batches->sum('quantity')) }} {{ $item->unit }}</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <div class="lg:col-span-2">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Form Transfer</h3>
                </x-slot:header>
                <form method="POST" action="{{ route('admin.inventory.transfer', $item) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="from_branch_id" value="{{ currentBranchId() }}">
                    <x-ui.select name="to_branch_id" label="Cabang Tujuan" :options="$branches" placeholder="Pilih Cabang" required />
                    <x-ui.input name="quantity" label="Jumlah" type="number" step="0.01" min="0.01" required help="Jumlah stok yang akan ditransfer" />
                    <x-ui.textarea name="notes" label="Catatan" rows="3" help="Catatan transfer (opsional)" />
                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <x-ui.button href="{{ route('admin.inventory.show', $item) }}" variant="ghost">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary">Transfer Stok</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-layouts.admin>
