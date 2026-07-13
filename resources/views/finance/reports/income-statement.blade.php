<x-layouts.admin title="Laporan Laba Rugi">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Laba Rugi</h1>
            <x-ui.button href="#" variant="primary">Export PDF</x-ui.button>
        </div>
    </x-slot:header>

    <x-ui.card class="max-w-3xl mx-auto">
        <x-slot:header>
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">ISTANA LAUNDRY</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Laporan Laba Rugi</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Periode: {{ date('F Y') }}</p>
            </div>
        </x-slot:header>

        <div class="space-y-8">
            <div>
                <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">PENDAPATAN</h4>
                <div class="space-y-2">
                    @forelse($revenues as $revenue)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400 pl-4">{{ $revenue->name }}</span>
                            <span class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($revenue->balance ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 dark:text-gray-400 pl-4">Tidak ada data pendapatan.</div>
                    @endforelse
                    <div class="flex items-center justify-between text-sm font-semibold pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-gray-900 dark:text-white">Total Pendapatan</span>
                        <span class="text-gray-900 dark:text-white">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">BEBAN</h4>
                <div class="space-y-2">
                    @forelse($expenses as $expense)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400 pl-4">{{ $expense->name }}</span>
                            <span class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($expense->balance ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 dark:text-gray-400 pl-4">Tidak ada data beban.</div>
                    @endforelse
                    <div class="flex items-center justify-between text-sm font-semibold pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-gray-900 dark:text-white">Total Beban</span>
                        <span class="text-gray-900 dark:text-white">Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="border-t-2 border-gray-900 dark:border-white pt-4">
                <div class="flex items-center justify-between text-base">
                    <span class="font-bold text-gray-900 dark:text-white">{{ $netIncome >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</span>
                    <span class="font-bold {{ $netIncome >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        Rp {{ number_format(abs($netIncome ?? 0), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </x-ui.card>
</x-layouts.admin>
