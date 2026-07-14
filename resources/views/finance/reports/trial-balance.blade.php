<x-layouts.admin title="Neraca Saldo">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Neraca Saldo</h1>
            @can('report.export')
            <x-ui.button href="#" variant="primary">Export PDF</x-ui.button>
            @endcan
        </div>
    </x-slot:header>

    @php
        $totalDebit = $accounts->sum(fn($a) => $a->balance > 0 ? $a->balance : 0);
        $totalCredit = $accounts->sum(fn($a) => $a->balance < 0 ? abs($a->balance) : 0);
    @endphp

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Neraca Saldo per {{ date('d/m/Y') }}</h3>
        </x-slot:header>
        <x-ui.table :headers="['Kode', 'Nama Akun', 'Debit', 'Kredit']">
            @forelse($accounts as $account)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ $account->code }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $account->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                        {{ $account->balance > 0 ? 'Rp ' . number_format($account->balance, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                        {{ $account->balance < 0 ? 'Rp ' . number_format(abs($account->balance), 0, ',', '.') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada data neraca saldo.</td>
                </tr>
            @endforelse
        </x-ui.table>
        <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-800">
            <div class="flex items-center justify-between font-semibold">
                <span class="text-sm text-gray-900 dark:text-white">Total</span>
                <div class="flex items-center gap-16">
                    <span class="text-sm text-gray-900 dark:text-white w-32 text-right">Rp {{ number_format($totalDebit, 0, ',', '.') }}</span>
                    <span class="text-sm text-gray-900 dark:text-white w-32 text-right">Rp {{ number_format($totalCredit, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </x-ui.card>
</x-layouts.admin>
