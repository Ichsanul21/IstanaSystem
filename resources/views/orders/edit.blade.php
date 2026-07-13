<x-layouts.admin title="Edit Order">
    <x-slot:header>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Order #{{ $order->order_number ?? $order['order_number'] ?? '-' }}</h1>
    </x-slot:header>

    <form method="POST" action="{{ route('admin.orders.update', $order->id ?? $order['id']) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-ui.card>
                    <x-slot:header>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Data Pelanggan</h2>
                    </x-slot:header>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input type="text" name="customer_name" label="Nama Pelanggan" :value="old('customer_name', $order->customer->name ?? '')" required />
                        <x-ui.input type="text" name="customer_phone" label="No. Telepon" :value="old('customer_phone', $order->customer->phone ?? '')" />
                        <x-ui.input type="email" name="customer_email" label="Email" :value="old('customer_email', $order->customer->email ?? '')" />
                        <x-ui.textarea name="customer_address" label="Alamat">{{ old('customer_address', $order->customer->address ?? '') }}</x-ui.textarea>
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <x-slot:header>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Item Pesanan</h2>
                    </x-slot:header>
                    <div class="space-y-3">
                        @forelse($order->items ?? $order['items'] ?? [] as $index => $item)
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->service?->name ?? '' ?? $item['service_name'] ?? '-' }}</p>
                                    <div class="flex gap-2 mt-1">
                                        <x-ui.input type="hidden" :name="'items['.$index.'][service_id]'" :value="$item->service_id ?? $item['service_pricing_id'] ?? ''" />
                                        <x-ui.input type="number" :name="'items['.$index.'][quantity]'" label="Qty" :value="old('items.'.$index.'.quantity', $item->quantity ?? $item['quantity'] ?? 1)" class="w-20" />
                                        <x-ui.input type="number" :name="'items['.$index.'][price_per_unit]'" label="Harga" :value="old('items.'.$index.'.price_per_unit', $item->price_per_unit ?? $item['unit_price'] ?? 0)" class="w-32" />
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Tidak ada item.</p>
                        @endforelse
                    </div>
                </x-ui.card>
            </div>

            <div class="space-y-6">
                <x-ui.card>
                    <x-slot:header>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Status & Catatan</h2>
                    </x-slot:header>
                    <div class="space-y-4">
                        @php $statuses = \App\Enums\OrderStatus::cases(); @endphp
                        <x-ui.select name="status" label="Status Order" :options="collect($statuses)->reject(fn($s) => $s === \App\Enums\OrderStatus::Draft)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()" :value="old('status', $order->status ?? $order['status'] ?? 'pending')" required />
                        <x-ui.select name="payment_status" label="Status Pembayaran" :options="['unpaid' => 'Belum Dibayar', 'paid' => 'Lunas']" :value="old('payment_status', $order->payment_status ?? $order['payment_status'] ?? 'unpaid')" required />
                        <x-ui.textarea name="notes" label="Catatan">{{ old('notes', $order->notes ?? $order['notes'] ?? '') }}</x-ui.textarea>
                    </div>
                </x-ui.card>

                <div class="flex gap-3">
                    <x-ui.button type="submit" variant="primary" class="flex-1">Simpan Perubahan</x-ui.button>
                    <x-ui.button href="{{ route('admin.orders.show', $order->id ?? $order['id']) }}" variant="ghost">Batal</x-ui.button>
                </div>
            </div>
        </div>
    </form>
</x-layouts.admin>