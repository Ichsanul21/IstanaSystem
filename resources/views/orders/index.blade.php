<x-layouts.admin title="Orders">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Orders</h1>
            <x-ui.button href="{{ route('admin.orders.create') }}" variant="primary">+ Buat Pesanan Baru</x-ui.button>
        </div>
    </x-slot:header>

    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-ui.select name="status" label="Status" placeholder="Semua Status" :options="['pending' => 'Baru', 'processing' => 'Diproses', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan']" :value="request('status')" />
            <x-ui.input type="date" name="date_from" label="Dari Tanggal" :value="request('date_from')" />
            <x-ui.input type="date" name="date_to" label="Sampai Tanggal" :value="request('date_to')" />
            <x-ui.input type="text" name="search" label="Cari" placeholder="Cari order / pelanggan..." :value="request('search')" />
            <div class="flex items-end gap-2 md:col-span-4">
                <x-ui.button type="submit" variant="primary">Filter</x-ui.button>
                <x-ui.button href="{{ route('admin.orders.index') }}" variant="ghost">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card class="p-0">
        <x-ui.table :headers="[
            ['label' => '# Order'],
            ['label' => 'Pelanggan'],
            ['label' => 'Layanan'],
            ['label' => 'Total'],
            ['label' => 'Status'],
            ['label' => 'Produksi'],
            ['label' => 'Tanggal'],
            ['label' => 'Aksi'],
        ]">
            @forelse($orders ?? [] as $order)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 text-sm font-medium text-primary">#{{ $order->order_number ?? $order['order_number'] ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $order->customer->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $order->items->pluck('servicePricing.service.name')->implode(', ') ?: '-' }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($order->grand_total ?? $order['total'] ?? 0, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @php
                            $s = $order->status ?? $order['status'] ?? '';
                            $sm = ['pending' => 'primary', 'processing' => 'warning', 'completed' => 'success', 'cancelled' => 'danger'];
                        @endphp
                        <x-ui.badge :variant="$sm[$s] ?? 'gray'">{{ $s }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach(($order->items ?? $order['items'] ?? []) as $item)
                                @php
                                    $ps = $item->production_status ?? $item['production_status'] ?? '';
                                    $psm = ['received' => 'gray', 'washed' => 'info', 'dried' => 'warning', 'ironed' => 'primary', 'packed' => 'info', 'ready_for_pickup' => 'success', 'picked_up' => 'success'];
                                @endphp
                                <x-ui.badge :variant="$psm[$ps] ?? 'gray'" size="sm">{{ $ps }}</x-ui.badge>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at ?? $order['created_at'] ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1">
                            <x-ui.button href="{{ route('admin.orders.show', $order->id ?? $order['id']) }}" variant="ghost" size="sm">Lihat</x-ui.button>
                            <x-ui.button href="{{ route('admin.orders.edit', $order->id ?? $order['id']) }}" variant="ghost" size="sm">Edit</x-ui.button>
                            <x-ui.button href="{{ route('admin.orders.print', $order->id ?? $order['id']) }}" variant="ghost" size="sm">Print</x-ui.button>
                            @if(($order->payment_status ?? $order['payment_status'] ?? '') !== 'paid')
                                <x-ui.button href="{{ route('admin.payments.create', $order->id ?? $order['id']) }}" variant="primary" size="sm">Bayar</x-ui.button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada data order.</td>
                </tr>
            @endforelse
        </x-ui.table>
        @if(method_exists($orders ?? [], 'links'))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $orders->links() }}
            </div>
        @endif
    </x-ui.card>
</x-layouts.admin>