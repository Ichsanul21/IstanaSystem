<x-layouts.admin title="Laporan Pendapatan">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Pendapatan</h1>
            <div class="flex items-center gap-3">
                @can('report.export')
                <x-ui.button href="#" variant="outline" size="sm">Export PDF</x-ui.button>
                <x-ui.button href="#" variant="primary" size="sm">Export Excel</x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot:header>

    <div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <x-ui.card class="border-l-4 border-l-primary">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pendapatan</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($revenues->sum('total') ?? 0, 0, ',', '.') }}</p>
            </x-ui.card>
            <x-ui.card class="border-l-4 border-l-blue-500">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Periode</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ ucfirst($period) }}</p>
            </x-ui.card>
            <x-ui.card class="border-l-4 border-l-green-500">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($revenues->count() > 0 ? $revenues->sum('total') / $revenues->count() : 0, 0, ',', '.') }}</p>
            </x-ui.card>
        </div>

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Pendapatan</h3>
            </x-slot:header>
            <x-ui.table :headers="['Tanggal', 'Pendapatan']">
                @forelse($revenues as $revenue)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $revenue->date }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Rp {{ number_format($revenue->total ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada data pendapatan.</td>
                </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>
    </div>
</x-layouts.admin>
