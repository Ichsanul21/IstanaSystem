@props(['items' => []])
<div class="rounded-xl border border-lo-gray dark:border-dark-700 bg-white dark:bg-dark-900 overflow-hidden">
    <div class="px-5 lg:px-8 py-4 border-b border-lo-gray dark:border-dark-700 flex items-center justify-between">
        <h3 class="text-lg font-bold text-dark dark:text-white">Low Stock Alert</h3>
        <x-ui.badge variant="warning" size="sm">{{ count($items) }} items</x-ui.badge>
    </div>
    <div class="divide-y divide-lo-gray dark:divide-dark-700">
        @forelse($items as $item)
        <div class="px-5 lg:px-8 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-dark-800 transition-colors">
            <div>
                <p class="text-sm font-medium text-dark dark:text-white">{{ $item->name }}</p>
                <p class="text-xs text-black/40 dark:text-white/40 mt-0.5">{{ $item->category ?? '—' }}</p>
            </div>
            <div class="text-right">
                <x-ui.badge variant="danger" size="sm">{{ $item->stock }} left</x-ui.badge>
            </div>
        </div>
        @empty
        <div class="px-5 lg:px-8 py-8 text-center text-sm text-black/40 dark:text-white/40">Semua stok aman</div>
        @endforelse
    </div>
</div>
