<x-layouts.admin title="Laporan Pajak">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Pajak</h1>
            <div class="flex items-center gap-3">
                <x-ui.button href="#" variant="outline" size="sm">Export PDF</x-ui.button>
                <x-ui.button href="#" variant="primary" size="sm">Export CSV</x-ui.button>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Log Pajak</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $logs->total() }}</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total PPN</p>
            <p class="text-2xl font-bold text-primary mt-1">Rp {{ number_format($totalTax, 0, ',', '.') }}</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Penjualan</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">Rp {{ number_format($totalTaxable, 0, ',', '.') }}</p>
        </x-ui.card>
    </div>

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Rincian Pajak</h3>
        </x-slot:header>
        <x-ui.table :headers="['Order', 'Jenis Pajak', 'Dasar Pengenaan', 'Nilai Pajak', 'Tanggal']">
            @forelse($logs as $log)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <td class="px-6 py-4 text-sm font-medium text-primary">#{{ $log->order->order_number ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $log->taxConfig->name ?? $log->tax_type ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Rp {{ number_format($log->base_amount ?? 0, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Rp {{ number_format($log->tax_amount ?? 0, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $log->created_at }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada data pajak.</td>
            </tr>
            @endforelse
        </x-ui.table>
        @if(method_exists($logs, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $logs->links() }}
            </div>
        @endif
    </x-ui.card>
</x-layouts.admin>
