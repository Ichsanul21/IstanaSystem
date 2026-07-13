<x-layouts.admin title="Arus Kas Harian">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Arus Kas Harian</h1>
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('admin.cash-flow.index') }}" class="flex items-center gap-3">
                    <x-ui.input type="date" name="date_from" value="{{ request('date_from', $today) }}" class="w-40" />
                    <x-ui.input type="date" name="date_to" value="{{ request('date_to', $today) }}" class="w-40" />
                    <x-ui.button type="submit" variant="primary" size="sm">Filter</x-ui.button>
                </form>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <x-ui.card class="border-l-4 border-l-primary">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo Awal</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($todayCashFlow->opening_balance ?? 0, 0, ',', '.') }}</p>
        </x-ui.card>
        <x-ui.card class="border-l-4 border-l-green-500">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kas Masuk (Hari Ini)</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">Rp {{ number_format($totalCashIn, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $cashInCount }} transaksi</p>
        </x-ui.card>
        <x-ui.card class="border-l-4 border-l-red-500">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kas Keluar (Hari Ini)</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">Rp {{ number_format($totalCashOut, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $cashOutCount }} transaksi</p>
        </x-ui.card>
        <x-ui.card class="border-l-4 border-l-blue-500">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo Akhir</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">Rp {{ number_format(($todayCashFlow->opening_balance ?? 0) + $totalCashIn - $totalCashOut, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Saldo awal + masuk - keluar</p>
        </x-ui.card>
    </div>

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Arus Kas</h3>
        </x-slot:header>
        <x-ui.table :headers="['Tanggal', 'Saldo Awal', 'Kas Masuk', 'Kas Keluar', 'Saldo Akhir', 'Selisih']">
            @forelse($cashFlows as $cf)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $cf->date }}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Rp {{ number_format($cf->opening_balance ?? 0, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-sm text-green-600">Rp {{ number_format($cf->total_cash_in ?? 0, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-sm text-red-600">Rp {{ number_format($cf->total_cash_out ?? 0, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($cf->closing_balance ?? 0, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-sm {{ ($cf->closing_balance ?? 0) >= ($cf->opening_balance ?? 0) ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format(($cf->closing_balance ?? 0) - ($cf->opening_balance ?? 0), 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada data arus kas</td>
            </tr>
            @endforelse
        </x-ui.table>
        @if($cashFlows->hasPages())
            <div class="mt-4">
                <x-ui.pagination :paginator="$cashFlows" />
            </div>
        @endif
    </x-ui.card>
</x-layouts.admin>
