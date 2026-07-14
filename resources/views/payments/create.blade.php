<x-layouts.admin title="Pembayaran">
    <x-slot:header>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pembayaran Order #{{ $order->order_number ?? $order['order_number'] ?? '-' }}</h1>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-ui.card>
            <x-slot:header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Ringkasan Order</h2>
            </x-slot:header>
            <dl class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">No. Order</span>
                    <span class="font-medium text-gray-900 dark:text-white">#{{ $order->order_number ?? $order['order_number'] ?? '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Pelanggan</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $order->customer->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Total Tagihan</span>
                    <span class="text-lg font-bold text-primary">Rp {{ number_format($order->grand_total ?? $order['total'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Sudah Dibayar</span>
                    <span class="font-medium text-green-600">Rp {{ number_format($order->payments->sum('amount') ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Sisa Tagihan</span>
                    <span class="font-bold text-lg {{ ($order->grand_total ?? 0) - ($order->payments->sum('amount') ?? 0) > 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rp {{ number_format(max(0, ($order->grand_total ?? 0) - ($order->payments->sum('amount') ?? 0)), 0, ',', '.') }}
                    </span>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Form Pembayaran</h2>
            </x-slot:header>
            <form method="POST" action="{{ route('admin.orders.payments.store', $order->id ?? $order['id']) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id ?? $order['id'] }}">
                <x-ui.input type="number" name="amount" label="Jumlah Dibayar" :value="old('amount', ($order->grand_total ?? 0) - ($order->paid_amount ?? 0))" required />
                <x-ui.select name="method" label="Metode Pembayaran" :options="['cash' => 'Tunai', 'transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'gateway' => 'Payment Gateway']" required />
                <x-ui.input type="text" name="reference" label="Referensi (opsional)" placeholder="No. referensi / bukti" />
                <x-ui.textarea name="notes" label="Catatan" />
                <x-ui.button type="submit" variant="primary" class="w-full">Konfirmasi Pembayaran</x-ui.button>
            </form>
        </x-ui.card>
    </div>
</x-layouts.admin>