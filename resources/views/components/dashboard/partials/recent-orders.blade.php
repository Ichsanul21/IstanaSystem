@props(['orders' => []])
<div class="rounded-xl border border-lo-gray dark:border-dark-700 bg-white dark:bg-dark-900 overflow-hidden">
    <div class="px-5 lg:px-8 py-4 border-b border-lo-gray dark:border-dark-700">
        <h3 class="text-lg font-bold text-dark dark:text-white">Recent Orders</h3>
    </div>
    <div class="divide-y divide-lo-gray dark:divide-dark-700">
        @forelse($orders as $order)
        <div class="px-5 lg:px-8 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-dark-800 transition-colors">
            <div>
                <p class="text-sm font-medium text-dark dark:text-white">#{{ $order->order_number }}</p>
                <p class="text-xs text-black/40 dark:text-white/40 mt-0.5">{{ $order->customer?->name ?? 'Walk-in' }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold text-dark dark:text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                @php $__os = \App\Enums\OrderStatus::tryFrom($order->status); @endphp
                <x-ui.badge variant="{{ $__os?->color() ?? 'gray' }}" size="sm" class="mt-1">{{ $__os?->label() ?? $order->status }}</x-ui.badge>
            </div>
        </div>
        @empty
        <div class="px-5 lg:px-8 py-8 text-center text-sm text-black/40 dark:text-white/40">Belum ada order</div>
        @endforelse
    </div>
</div>
