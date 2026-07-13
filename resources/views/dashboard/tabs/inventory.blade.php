@props(['stockValue' => 0, 'lowStockItems' => [], 'stockMovement' => []])

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
    <x-dashboard.partials.metric-card
        label="Nilai Stok"
        value="Rp {{ number_format($stockValue, 0, ',', '.') }}"
        icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>'
    />
    <x-dashboard.partials.metric-card
        label="Item Stok Menipis"
        :value="count($lowStockItems)"
        icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>'
    />
    <x-dashboard.partials.metric-card
        label="Total Transaksi Stok"
        :value="count($stockMovement)"
        icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75H17.25m-9.75 0h9.75"/></svg>'
    />
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <x-dashboard.partials.low-stock-alert :items="$lowStockItems" />
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-bold">Mutasi Stok Terbaru</h3>
        </x-slot:header>
        @if(count($stockMovement) > 0)
        <div class="space-y-3">
            @foreach($stockMovement as $movement)
            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $movement['item'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $movement['reference'] }} &middot; {{ $movement['created_at'] }}</p>
                </div>
                <x-ui.badge :variant="$movement['type'] === 'in' ? 'success' : 'danger'">
                    {{ $movement['type'] === 'in' ? '+' : '-' }}{{ number_format($movement['quantity'], 0, ',', '.') }}
                </x-ui.badge>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">Belum ada mutasi stok.</p>
        @endif
    </x-ui.card>
</div>
