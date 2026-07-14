<x-layouts.admin title="Laporan Pelanggan">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Pelanggan</h1>
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
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pelanggan</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalCustomers }}</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transaksi</p>
            <p class="text-2xl font-bold text-primary mt-1">{{ $totalOrders }}</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Belanja</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata Order</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $totalCustomers > 0 ? number_format($totalOrders / $totalCustomers, 1) : 0 }}</p>
        </x-ui.card>
    </div>

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Pelanggan</h3>
        </x-slot:header>
        <x-ui.table :headers="['Nama', 'Telepon', 'Tier', 'Total Transaksi', 'Total Belanja']">
            @forelse($customers as $customer)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $customer->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $customer->phone ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $customer->membershipTier->name ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $customer->orders_count ?? 0 }}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Rp {{ number_format($customer->orders_sum_total ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada data pelanggan.</td>
            </tr>
            @endforelse
        </x-ui.table>
        @if(method_exists($customers, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $customers->links() }}
            </div>
        @endif
    </x-ui.card>
</x-layouts.admin>
