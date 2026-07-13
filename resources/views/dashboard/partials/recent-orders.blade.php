@props(['orders' => []])
<x-ui.card>
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold">Pesanan Terbaru</h2>
            <x-ui.button variant="ghost" size="sm" href="{{ route('admin.orders.index') }}">Lihat Semua</x-ui.button>
        </div>
    </x-slot:header>
    <x-ui.table :headers="[
        ['label' => '#Order'],
        ['label' => 'Pelanggan'],
        ['label' => 'Total'],
        ['label' => 'Status'],
        ['label' => 'Tanggal'],
    ]">
        @forelse($orders as $order)
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <td class="px-6 py-4 text-sm font-medium text-primary">#{{ $order->order_number }}</td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $order->customer?->name ?? '-' }}</td>
            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
            <td class="px-6 py-4">
                @php
                $statusMap = ['draft' => 'gray', 'pending' => 'warning', 'processing' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
                $statusLabel = ['draft' => 'Draft', 'pending' => 'Baru', 'processing' => 'Diproses', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                @endphp
                <x-ui.badge :variant="$statusMap[$order->status] ?? 'gray'">{{ $statusLabel[$order->status] ?? $order->status }}</x-ui.badge>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada pesanan.</td>
        </tr>
        @endforelse
    </x-ui.table>
</x-ui.card>
