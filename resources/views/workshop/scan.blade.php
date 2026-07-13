@php
    $statuses = \App\Enums\ProductionStatus::ordered();
    $statusLabels = [];
    foreach (\App\Enums\ProductionStatus::cases() as $s) {
        $statusLabels[$s->value] = $s->label();
    }
@endphp

<x-layouts.admin title="Scan QR Produksi">
    <x-slot:header>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Scan QR Produksi</h1>
    </x-slot:header>

    <div class="max-w-lg mx-auto space-y-4">
        <x-ui.card>
            <x-slot:header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pindai QR Code Item</h2>
            </x-slot:header>
            <div id="qr-reader" class="w-full overflow-hidden rounded-lg" style="min-height: 300px;"></div>
            <div id="qr-reader-results" class="mt-4 text-center"></div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Input Manual</h2>
            </x-slot:header>
            <div x-data="manualInput()" class="space-y-4">
                <form method="GET" action="{{ route('admin.workshop.scan') }}" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Token QR / Nomor Order</label>
                        <div class="flex gap-3">
                            <x-ui.input type="text" name="token" placeholder="Masukkan token QR atau nomor order..." class="flex-1"
                                        x-model="token" required />
                            <x-ui.button type="submit" variant="primary">Cari</x-ui.button>
                        </div>
                    </div>
                </form>

                @if(!empty($scannedItem))
                    @php
                        $item = $scannedItem;
                        $currentStatus = $item->productionStatuses->first()?->to_status ?? null;
                        $currentSeq = 0;
                        foreach ($statuses as $s) {
                            if ($s->value === $currentStatus) {
                                $currentSeq = $s->sequence();
                                break;
                            }
                        }
                        $allowed = $currentStatus
                            ? \App\Enums\ProductionStatus::allowedTransitionsFrom(\App\Enums\ProductionStatus::tryFrom($currentStatus))
                            : [\App\Enums\ProductionStatus::Received];
                    @endphp

                    <div class="p-4 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-800">
                        <p class="text-sm text-green-800 dark:text-green-200 font-medium mb-2">Item Ditemukan!</p>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-green-700 dark:text-green-300">Layanan:</span>
                                <span class="font-medium text-green-900 dark:text-green-100">{{ $item->service?->name ?? '' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-green-700 dark:text-green-300">Order:</span>
                                <a href="{{ route('admin.workshop.order-detail', $item->order_id) }}" class="font-medium text-primary hover:underline">
                                    #{{ $item->order->order_number }}
                                </a>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-green-700 dark:text-green-300">Status:</span>
                                <x-ui.badge :variant="match($currentStatus) { 'picked_up' => 'success', 'ready_for_pickup' => 'success', default => 'info' }">
                                    {{ $statusLabels[$currentStatus] ?? 'Belum Diproses' }}
                                </x-ui.badge>
                            </div>
                        </div>

                        <div class="flex items-center gap-1 mt-3">
                            @foreach($statuses as $s)
                                @php
                                    $done = $currentStatus && $s->sequence() <= $currentSeq;
                                    $active = $s->value === $currentStatus;
                                @endphp
                                <div class="flex-1 flex flex-col items-center">
                                    <div class="w-full h-1.5 rounded-full {{ $done ? 'bg-primary' : 'bg-gray-200 dark:bg-gray-700' }} {{ $active ? 'ring-2 ring-primary/30' : '' }}"></div>
                                    <span class="text-[9px] mt-0.5 text-gray-500 dark:text-gray-400 {{ $active ? 'text-primary font-bold' : '' }}">{{ $s->label() }}</span>
                                </div>
                            @endforeach
                        </div>

                        @if(count($allowed) > 0)
                            <form method="POST" action="{{ route('admin.workshop.update-status', $item->id) }}" class="mt-4 space-y-3">
                                @csrf
                                <input type="hidden" name="status" value="{{ $allowed[0]->value }}">
                                <x-ui.textarea name="notes" label="Catatan (opsional)" rows="2" />
                                <x-ui.button type="submit" variant="primary" class="w-full">
                                    Lanjutkan ke {{ $allowed[0]->label() }}
                                </x-ui.button>
                            </form>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-4">Status sudah final.</p>
                        @endif
                    </div>
                @endif

                @if(!empty($scanError))
                    <div class="p-4 bg-red-50 dark:bg-red-900/30 rounded-lg border border-red-200 dark:border-red-800">
                        <p class="text-sm text-red-800 dark:text-red-200">{{ $scanError }}</p>
                    </div>
                @endif
            </div>
        </x-ui.card>

        @if(session('success'))
            <div class="p-4 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-800">
                <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        function manualInput() {
            return {
                token: '{{ request("token", "") }}'
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            function onScanSuccess(decodedText, decodedResult) {
                document.getElementById('qr-reader-results').innerHTML =
                    '<div class="p-4 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-800">' +
                    '<p class="text-sm text-green-800 dark:text-green-200">QR Terdeteksi: <strong>' + decodedText + '</strong></p>' +
                    '<a href="' + decodedText + '" class="mt-2 inline-block text-sm text-primary hover:underline">Buka Item</a>' +
                    '</div>';
            }

            function onScanError(errorMessage) {}

            try {
                const html5QrCode = new Html5Qrcode("qr-reader");
                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    onScanSuccess,
                    onScanError
                ).catch(function(err) {
                    document.getElementById('qr-reader').innerHTML =
                        '<div class="p-6 text-center text-sm text-gray-500">' +
                        'Tidak dapat mengakses kamera. Gunakan input manual di bawah.' +
                        '</div>';
                });
            } catch(e) {
                document.getElementById('qr-reader').innerHTML =
                    '<div class="p-6 text-center text-sm text-gray-500">' +
                    'Kamera tidak tersedia. Gunakan input manual di bawah.' +
                    '</div>';
            }
        });
    </script>
    @endpush
</x-layouts.admin>
