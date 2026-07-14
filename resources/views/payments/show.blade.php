<x-layouts.admin title="Detail Pembayaran">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Pembayaran</h1>
            <x-ui.button href="{{ route('admin.orders.receipt', $payment->order_id ?? $payment['order_id']) }}" variant="outline" size="sm">Print Receipt</x-ui.button>
        </div>
    </x-slot:header>

    <div class="max-w-2xl mx-auto">
        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Pembayaran</h2>
                    @php $hasRefund = $payment->refunds->isNotEmpty(); @endphp
                    <x-ui.badge :variant="$hasRefund ? 'danger' : 'success'">{{ $hasRefund ? 'Dikembalikan' : 'Lunas' }}</x-ui.badge>
                </div>
            </x-slot:header>
            <dl class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">ID Pembayaran</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">#{{ $payment->id ?? $payment['id'] ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Order</dt>
                        <dd class="text-sm font-medium text-primary">#{{ $payment->order_number ?? $payment['order_number'] ?? $payment->order->order_number ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Metode Pembayaran</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->method ?? $payment['payment_method'] ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Referensi</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->reference ?? $payment['reference'] ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Jumlah</dt>
                        <dd class="text-lg font-bold text-primary">Rp {{ number_format($payment->amount ?? $payment['amount'] ?? 0, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Dibayar Pada</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->paid_at ?? $payment['paid_at'] ?? $payment->created_at ?? $payment['created_at'] ?? '-' }}</dd>
                    </div>
                </div>
                @if($payment->notes ?? $payment['notes'] ?? null)
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Catatan</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ $payment->notes ?? $payment['notes'] }}</dd>
                    </div>
                @endif
            </dl>
        </x-ui.card>
    </div>
</x-layouts.admin>