<x-layouts.admin title="Laporan Order">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Order</h1>
            <div class="flex items-center gap-3">
                @can('report.export')
                <x-ui.button href="#" variant="outline" size="sm">Export PDF</x-ui.button>
                <x-ui.button href="#" variant="primary" size="sm">Export Excel</x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Order</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalPending + $totalProcessing + $totalCompleted }}</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $totalPending }}</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Diproses</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $totalProcessing }}</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Siap / Diambil</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $totalCompleted }}</p>
        </x-ui.card>
    </div>

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Order</h3>
        </x-slot:header>
        <x-ui.table :headers="['# Order', 'Pelanggan', 'Total', 'Status', 'Tanggal']">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <td class="px-6 py-4 text-sm font-medium text-primary">#{{ $order->order_number }}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $order->customer->name ?? '-' }}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($order->grand_total ?? 0, 0, ',', '.') }}</td>
                @php $os = \App\Enums\OrderStatus::tryFrom($order->status); @endphp
                <td class="px-6 py-4"><x-ui.badge :variant="$os?->color() ?? 'gray'">{{ $os?->label() ?? $order->status }}</x-ui.badge></td>
                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada data order.</td>
            </tr>
            @endforelse
        </x-ui.table>
        @if(method_exists($orders, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $orders->links() }}
            </div>
        @endif
    </x-ui.card>
</x-layouts.admin>
