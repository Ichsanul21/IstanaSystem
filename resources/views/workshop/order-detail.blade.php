@php
    $statuses = \App\Enums\ProductionStatus::ordered();
    $allStatuses = \App\Enums\ProductionStatus::cases();
    $statusLabels = [];
    foreach ($allStatuses as $s) {
        $statusLabels[$s->value] = $s->label();
    }
    $currentItemStatus = null;
    if ($orderItem) {
        $latest = $orderItem->statusLogs->first();
        $currentItemStatus = $latest?->productionStatus?->code;
    }
    $currentSeq = 0;
    foreach ($statuses as $s) {
        if ($s->value === $currentItemStatus) {
            $currentSeq = $s->sequence();
            break;
        }
    }
@endphp

<x-layouts.admin title="Detail Order Workshop">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Order</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">#{{ $order->order_number }} &mdash; {{ $order->customer->name ?? '-' }}</p>
            </div>
            <x-ui.button href="{{ route('admin.workshop.index') }}" variant="ghost" size="sm">Kembali</x-ui.button>
        </div>
    </x-slot:header>

    @if(session('success'))
        <div class="mb-4">
            <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4">
            <x-ui.alert type="danger">{{ session('error') }}</x-ui.alert>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Order</h2>
                </x-slot:header>
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Nomor Order</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">#{{ $order->order_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Pelanggan</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->customer->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Cabang</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->branch->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Status Order</dt>
                        <dd>
                            @php $os = \App\Enums\OrderStatus::tryFrom($order->status); @endphp
                            <x-ui.badge :variant="$os?->color() ?? 'gray'">{{ $os?->label() ?? $order->status }}</x-ui.badge>
                        </dd>
                    </div>
                </dl>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Item & Status Produksi</h2>
                </x-slot:header>

                @if($orderItem)
                    @php
                        $item = $orderItem;
                        $itemStatus = $currentItemStatus;
                        $allowed = $itemStatus
                            ? \App\Enums\ProductionStatus::allowedTransitionsFrom(\App\Enums\ProductionStatus::tryFrom($itemStatus))
                            : [\App\Enums\ProductionStatus::Terima];
                    @endphp

                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->service?->name ?? '' }}</span>
                            <x-ui.badge variant="info">Qty: {{ $item->quantity }}</x-ui.badge>
                        </div>

                        <div class="flex items-center gap-1">
                            @foreach($statuses as $idx => $s)
                                @php
                                    $completed = $itemStatus && $s->sequence() <= $currentSeq;
                                    $active = $s->value === $itemStatus;
                                @endphp
                                <div class="flex-1 flex flex-col items-center">
                                    <div class="w-full h-2 rounded-full {{ $completed ? 'bg-primary' : 'bg-gray-200 dark:bg-gray-700' }} {{ $active ? 'ring-2 ring-primary/30' : '' }}"></div>
                                    <span class="text-[10px] mt-1 text-gray-500 dark:text-gray-400 {{ $active ? 'text-primary font-bold' : '' }}">{{ $s->label() }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.workshop.update-status', $item->id) }}"
                          x-data="{ showWa: false, waPhone: '', waMsg: '', showNotes: false }"
                          @if(session('success') && session('wa_notify')) x-init="$nextTick(() => showWa = true)" @endif>
                        @csrf
                        <div class="space-y-3">
                            @if(count($allowed) > 0)
                                @foreach($allowed as $nextStatus)
                                    <input type="hidden" name="status" value="{{ $nextStatus->value }}">
                                    <x-ui.button type="submit" variant="primary" class="w-full">
                                        Lanjutkan ke {{ $nextStatus->label() }}
                                    </x-ui.button>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-2">Status sudah final.</p>
                            @endif
                        </div>
                        <div class="mt-3">
                            <x-ui.textarea name="notes" label="Catatan (opsional)" rows="2" />
                        </div>
                    </form>

                    @if(session('success') && session('wa_notify'))
                        @php
                            $waPhone = $order->customer->phone ?? '';
                            $waClean = preg_replace('/[^0-9]/', '', $waPhone);
                            if (str_starts_with($waClean, '0')) {
                                $waClean = '62' . substr($waClean, 1);
                            }
                            $waText = urlencode("Order #{$order->order_number} status produksi telah diperbarui. Terima kasih!");
                            $waLink = "https://wa.me/{$waClean}?text={$waText}";
                        @endphp
                        <x-ui.modal name="wa-notify" title="Kirim Notifikasi WhatsApp">
                            <x-slot:body>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                    Kirim notifikasi status ke <strong>{{ $order->customer->name ?? '-' }}</strong> via WhatsApp?
                                </p>
                                @if($waClean)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        Kirim via WhatsApp
                                    </a>
                                @else
                                    <p class="text-sm text-gray-500">Nomor telepon pelanggan tidak tersedia.</p>
                                @endif
                            </x-slot:body>
                            <x-slot:footer>
                                <x-ui.button href="{{ route('admin.workshop.items.show', $orderItem->id) }}" variant="ghost" size="sm">Lihat Item</x-ui.button>
                                <x-ui.button href="{{ route('admin.workshop.index') }}" variant="primary" size="sm">Kembali ke Workshop</x-ui.button>
                            </x-slot:footer>
                        </x-ui.modal>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'wa-notify' }));
                            });
                        </script>
                    @endif
                @else
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            @php
                                $itemLatest = $item->statusLogs->first();
                                $itemStatus = $itemLatest?->productionStatus?->code;
                                $itemSeq = 0;
                                foreach ($statuses as $s) {
                                    if ($s->value === $itemStatus) {
                                        $itemSeq = $s->sequence();
                                        break;
                                    }
                                }
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->service?->name ?? '' }}</p>
                                    <div class="flex items-center gap-1 mt-1">
                                        @foreach($statuses as $s)
                                            @php
                                                $done = $itemStatus && $s->sequence() <= $itemSeq;
                                            @endphp
                                            <div class="w-3 h-3 rounded-full {{ $done ? 'bg-primary' : 'bg-gray-200 dark:bg-gray-700' }}" title="{{ $s->label() }}"></div>
                                        @endforeach
                                    </div>
                                </div>
                                <x-ui.badge :variant="$itemStatus === 'DIAMBIL' ? 'success' : ($itemStatus ? 'info' : 'gray')">
                                    {{ $itemStatus ? $statusLabels[$itemStatus] ?? $itemStatus : 'Belum' }}
                                </x-ui.badge>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Timeline Produksi</h2>
                </x-slot:header>
                <div class="space-y-4">
                    @php
                        $timeline = $orderItem
                            ? $orderItem->statusLogs->sortByDesc('created_at')
                            : $order->items->flatMap->statusLogs->sortByDesc('created_at');
                    @endphp
                    @forelse($timeline as $history)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-primary">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                </div>
                                <div class="w-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                            <div class="pb-4">
                                @php
                                    $prevIndex = $loop->index + 1 < $timeline->count() ? $timeline->values()[$loop->index + 1] : null;
                                    $fromLabel = $prevIndex?->productionStatus?->code ? ($statusLabels[$prevIndex->productionStatus->code] ?? $prevIndex->productionStatus->code) : '-';
                                    $toLabel = $history->productionStatus ? ($statusLabels[$history->productionStatus->code] ?? $history->productionStatus->code) : '-';
                                @endphp
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $fromLabel }} &rarr; {{ $toLabel }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $history->created_at->format('d M Y H:i') }}</p>
                                @if($history->notes)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $history->notes }}</p>
                                @endif
                                @if($history->scannedBy)
                                    <p class="text-xs text-gray-400 mt-1">oleh {{ $history->scannedBy->name }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada riwayat produksi.</p>
                    @endforelse
                </div>
            </x-ui.card>

            @if($orderItem && $orderItem->qr_code)
                <x-ui.card>
                    <x-slot:header>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">QR Code</h2>
                    </x-slot:header>
                    <div class="flex justify-center">
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            {!! $orderItem->qr_code !!}
                        </div>
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>
</x-layouts.admin>
