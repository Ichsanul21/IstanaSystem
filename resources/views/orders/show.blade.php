<x-layouts.admin title="Detail Order">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Order #{{ $order->order_number ?? $order['order_number'] ?? '-' }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at ?? $order['created_at'] ?? '' }}</p>
            </div>
            <div class="flex items-center gap-2">
                @can('order.update')
                <x-ui.button href="{{ route('admin.orders.edit', $order->id ?? $order['id']) }}" variant="secondary" size="sm">Edit</x-ui.button>
                @endcan
                <x-ui.button href="{{ route('admin.orders.receipt', $order->id ?? $order['id']) }}" variant="outline" size="sm">Print</x-ui.button>
                @if(($order->payment_status ?? $order['payment_status'] ?? '') !== 'paid')
                    @can('payment.create')
                    <x-ui.button href="{{ route('admin.orders.payments.create', $order->id ?? $order['id']) }}" variant="primary" size="sm">Bayar</x-ui.button>
                    @endcan
                @endif
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Order</h2>
                </x-slot:header>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">No. Order</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">#{{ $order->order_number ?? $order['order_number'] ?? '-' }}</dd>
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
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                        <dd>
                            @php $s = $order->status ?? $order['status'] ?? ''; $os = \App\Enums\OrderStatus::tryFrom($s); @endphp
                            <x-ui.badge :variant="$os?->color() ?? 'gray'">{{ $os?->label() ?? $s }}</x-ui.badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Tanggal Dibuat</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->created_at ?? $order['created_at'] ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Estimasi Selesai</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->items->first()?->servicePricing?->estimated_days ? now()->addDays($order->items->first()->servicePricing->estimated_days)->format('d/m/Y') : '-' }}</dd>
                    </div>
                </dl>
                @if($order->notes ?? $order['notes'] ?? null)
                    <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Catatan:</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $order->notes ?? $order['notes'] }}</p>
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Item Pesanan</h2>
                </x-slot:header>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Layanan</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Harga</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                            @forelse($order->items ?? $order['items'] ?? [] as $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $item->service_name ?? $item['service_name'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-center text-gray-700 dark:text-gray-300">{{ $item->quantity ?? $item['quantity'] ?? 1 }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($item->unit_price ?? $item['unit_price'] ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">Rp {{ number_format(($item->quantity ?? 1) * ($item->unit_price ?? 0), 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada item.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-sm font-semibold text-right text-gray-900 dark:text-white">Total</td>
                                <td class="px-4 py-3 text-sm font-bold text-right text-primary">Rp {{ number_format($order->total ?? $order['total'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Status</h2>
                </x-slot:header>
                <div class="space-y-4">
                    @forelse($statusTimeline ?? [] as $history)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                </div>
                                <div class="w-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                            <div class="pb-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $history->productionStatus?->name ?? ($history['to_status'] ?? '') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $history->created_at ?? $history['created_at'] ?? '' }}</p>
                                @if($history->note ?? $history['note'] ?? null)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $history->note ?? $history['note'] }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada riwayat status.</p>
                    @endforelse
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Pembayaran</h2>
                </x-slot:header>
                <dl class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Metode</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $order->payments->first()?->payment_method ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Total</span>
                        <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($order->total ?? $order['total'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Dibayar</span>
                        <span class="font-medium text-green-600">Rp {{ number_format($order->payments->sum('amount') ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Status</span>
                        @php
                            $ps = $order->payment_status ?? $order['payment_status'] ?? 'unpaid';
                        @endphp
                        <x-ui.badge :variant="$ps === 'paid' ? 'success' : 'warning'">{{ $ps === 'paid' ? 'Lunas' : 'Belum Dibayar' }}</x-ui.badge>
                    </div>
                </dl>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Status Produksi</h2>
                </x-slot:header>
                <div class="space-y-4">
                    @forelse($order->items ?? $order['items'] ?? [] as $item)
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->service_name ?? $item['service_name'] ?? '-' }}</p>
                                @php
                                    $ps = $item->production_status ?? $item['production_status'] ?? 'received';
                                    $psm = ['draft' => 'gray', 'pending' => 'warning', 'received' => 'gray', 'washed' => 'info', 'dried' => 'warning', 'ironed' => 'primary', 'packed' => 'info', 'ready_for_pickup' => 'success', 'picked_up' => 'success', 'cancelled' => 'danger'];
                                    $psl = ['draft' => 'Draft', 'pending' => 'Baru', 'received' => 'Diterima', 'washed' => 'Dicuci', 'dried' => 'Dikeringkan', 'ironed' => 'Disetrika', 'packed' => 'Dikemas', 'ready_for_pickup' => 'Siap Ambil', 'picked_up' => 'Diambil', 'cancelled' => 'Dibatalkan'];
                                @endphp
                                <x-ui.badge :variant="$psm[$ps] ?? 'gray'" size="sm">{{ $psl[$ps] ?? $ps }}</x-ui.badge>
                            </div>
                            <div class="flex justify-center">
                                <div class="bg-white dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-700">
                                    {!! $item->qr_code ?? $item['qr_code'] ?? '<div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs text-gray-500">QR</div>' !!}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Tidak ada item.</p>
                    @endforelse
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.admin>