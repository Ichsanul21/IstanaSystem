<x-layouts.admin title="Workshop / Produksi">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Workshop / Produksi</h1>
            @can('workshop.scan')
            <x-ui.button href="{{ route('admin.workshop.scan') }}" variant="primary" size="sm">
                Scan QR
            </x-ui.button>
            @endcan
        </div>
    </x-slot:header>

    @php
        $statuses = \App\Enums\ProductionStatus::ordered();
        $columns = [];
        foreach ($statuses as $s) {
            $columns[$s->value] = $s->label();
        }
    @endphp

    <div x-data="workshopBoard()" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 overflow-x-auto pb-4">
        @foreach($columns as $key => $label)
            <div class="min-w-[160px] bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $label }}</h3>
                    <span class="text-xs font-medium text-gray-500 bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ count($grouped[$key] ?? []) }}</span>
                </div>
                <div class="space-y-2 min-h-[200px]">
                    @forelse($grouped[$key] ?? [] as $item)
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-600">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item->service?->name ?? $item['service_name'] ?? 'Item' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">#{{ $item->order_number ?? $item['order_number'] ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->time_elapsed ?? $item['time_elapsed'] ?? '' }}</p>
                                </div>
                                <a href="{{ route('admin.workshop.items.show', $item) }}"
                                   class="shrink-0 text-gray-400 hover:text-primary transition-colors"
                                   title="Detail">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
                                </a>
                            </div>
                            @if($key !== 'picked_up')
                                @canany(['workshop.update_status', 'quality_check'])
                                <form method="POST" action="{{ route('admin.workshop.update-status', $item->id ?? $item['id']) }}" class="mt-2">
                                    @csrf
                                    @php
                                        $currentEnum = \App\Enums\ProductionStatus::tryFrom($key);
                                        $nextStatus = $currentEnum?->next()?->value ?? 'SIAP';
                                    @endphp
                                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                                    <x-ui.button type="submit" size="sm" variant="primary" class="w-full text-xs">Lanjutkan</x-ui.button>
                                </form>
                                @endcanany
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-6">Tidak ada item</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
    <script>
        function workshopBoard() {
            return {};
        }
    </script>
    @endpush
</x-layouts.admin>