@php
    $statuses = \App\Enums\ProductionStatus::ordered();
    $statusLabels = [];
    foreach (\App\Enums\ProductionStatus::cases() as $s) {
        $statusLabels[$s->value] = $s->label();
    }
    $currentLog = $item->statusLogs->first();
    $currentStatus = $currentLog?->productionStatus?->code ?? null;
    $currentSeq = 0;
    foreach ($statuses as $s) {
        if ($s->value === $currentStatus) {
            $currentSeq = $s->sequence();
            break;
        }
    }
    $allowed = $currentStatus
        ? \App\Enums\ProductionStatus::allowedTransitionsFrom(\App\Enums\ProductionStatus::tryFrom($currentStatus))
        : [\App\Enums\ProductionStatus::Terima];
@endphp

<x-layouts.admin title="Detail Produksi Item">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Produksi Item</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->service?->name ?? '' }} - #{{ $item->order->order_number }}</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-6">
            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Item</h2>
                </x-slot:header>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Layanan</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->service?->name ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Order</dt>
                        <dd class="text-sm font-medium text-primary">
                            <a href="{{ route('admin.workshop.order-detail', $item->order_id) }}" class="hover:underline">
                                #{{ $item->order->order_number }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Qty</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->quantity }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Status Produksi</dt>
                        <dd>
                            @php
                                $psm = ['TERIMA' => 'gray', 'PILAH' => 'info', 'CUCI' => 'warning', 'KERING' => 'primary', 'LIPAT' => 'info', 'CEK' => 'info', 'SIAP' => 'success', 'DIAMBIL' => 'success'];
                            @endphp
                            <x-ui.badge :variant="$psm[$currentStatus] ?? 'gray'">{{ $statusLabels[$currentStatus] ?? 'Belum Diproses' }}</x-ui.badge>
                        </dd>
                    </div>
                </dl>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Progres Produksi</h2>
                </x-slot:header>
                <div class="flex items-center gap-1 py-2">
                    @foreach($statuses as $s)
                        @php
                            $done = $currentStatus && $s->sequence() <= $currentSeq;
                            $active = $s->value === $currentStatus;
                        @endphp
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full h-2 rounded-full {{ $done ? 'bg-primary' : 'bg-gray-200 dark:bg-gray-700' }} {{ $active ? 'ring-2 ring-primary/30' : '' }}"></div>
                            <span class="text-[10px] mt-1 text-gray-500 dark:text-gray-400 {{ $active ? 'text-primary font-bold' : '' }}">{{ $s->label() }}</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card x-data="{ showWa: false }" @status-updated.window="showWa = true">
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Update Status Produksi</h2>
                </x-slot:header>
                <form method="POST" action="{{ route('admin.workshop.update-status', $item->id) }}" class="space-y-4"
                      x-ref="statusForm"
                      @submit.prevent="
                          $refs.statusForm.$el.submit();
                          $dispatch('status-updated');
                      ">
                    @csrf
                    @if(count($allowed) > 0)
                        <x-ui.select name="status" label="Status" :options="collect($allowed)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()" required />
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status sudah final, tidak ada transisi yang tersedia.</p>
                    @endif
                    <x-ui.textarea name="notes" label="Catatan" />
                    @if(count($allowed) > 0)
                        <x-ui.button type="submit" variant="primary" class="w-full">Update Status</x-ui.button>
                    @endif
                </form>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Timeline Produksi</h2>
                </x-slot:header>
                <div class="space-y-4">
                    @forelse($item->statusLogs as $history)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-primary">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                </div>
                                <div class="w-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                            <div class="pb-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $statusLabels[$history->productionStatus?->code] ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $history->created_at->format('d M Y H:i') }}</p>
                                @if($history->note)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $history->note }}</p>
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

            @if($item->qr_token)
                <x-ui.card>
                    <x-slot:header>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">QR Code</h2>
                    </x-slot:header>
                    <div class="flex justify-center">
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            {{ $item->qr_token }}
                        </div>
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>

    @php
        $waPhone = $item->order->customer->phone ?? '';
        $waClean = preg_replace('/[^0-9]/', '', $waPhone);
        if (str_starts_with($waClean, '0')) {
            $waClean = '62' . substr($waClean, 1);
        }
        $waText = urlencode("Order #{$item->order->order_number} status produksi telah diperbarui. Terima kasih!");
        $waLink = "https://wa.me/{$waClean}?text={$waText}";
    @endphp

    <x-ui.modal name="wa-success" title="Status Berhasil Diperbarui">
        <x-slot:body>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Status produksi telah berhasil diperbarui.
            </p>
            @if($waClean)
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                    Kirim notifikasi ke <strong>{{ $item->order->customer->name ?? '-' }}</strong> via WhatsApp?
                </p>
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
            <x-ui.button href="{{ route('admin.workshop.index') }}" variant="primary" size="sm">Kembali ke Workshop</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</x-layouts.admin>
