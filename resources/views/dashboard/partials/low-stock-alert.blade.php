@props(['items' => []])
<x-ui.card>
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-red-600 dark:text-red-400">Peringatan Stok Menipis</h2>
            <x-ui.button variant="ghost" size="sm" href="{{ route('admin.inventory.index') }}">Kelola</x-ui.button>
        </div>
    </x-slot:header>
    @if(count($items) > 0)
    <div class="space-y-3">
        @foreach($items as $item)
        <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item['code'] }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold text-red-600 dark:text-red-400">{{ number_format($item['stock'], 0, ',', '.') }} {{ $item['unit'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Min. {{ number_format($item['min_stock'], 0, ',', '.') }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">Semua stok dalam kondisi aman.</p>
    @endif
</x-ui.card>
