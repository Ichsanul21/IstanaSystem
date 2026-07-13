<x-layouts.admin title="Workshop / Produksi">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Workshop / Produksi</h1>
            <x-ui.button href="{{ route('admin.workshop.scan') }}" variant="primary" size="sm">
                Scan QR
            </x-ui.button>
        </div>
    </x-slot:header>

    @php
        $columns = [
            'received' => 'Diterima',
            'washed' => 'Dicuci',
            'dried' => 'Dikeringkan',
            'ironed' => 'Disetrika',
            'packed' => 'Dikemas',
            'ready_for_pickup' => 'Siap Ambil',
            'picked_up' => 'Selesai',
        ];
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
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-600 cursor-pointer hover:border-primary/50 transition-colors"
                             x-on:click="window.location.href='{{ route('admin.workshop.items.show', $item) }}'">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item->service?->name ?? $item['service_name'] ?? 'Item' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">#{{ $item->order_number ?? $item['order_number'] ?? '-' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $item->time_elapsed ?? $item['time_elapsed'] ?? '' }}</p>
                            @if($key !== 'picked_up')
                                <form method="POST" action="{{ route('admin.workshop.update-status', $item->id ?? $item['id']) }}" class="mt-2" x-on:click.stop>
                                    @csrf
                                    @php
                                        $nextMap = ['received' => 'washed', 'washed' => 'dried', 'dried' => 'ironed', 'ironed' => 'packed', 'packed' => 'ready_for_pickup', 'ready_for_pickup' => 'picked_up'];
                                        $nextStatus = $nextMap[$key] ?? 'picked_up';
                                    @endphp
                                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                                    <x-ui.button type="submit" size="sm" variant="primary" class="w-full text-xs">Lanjutkan</x-ui.button>
                                </form>
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