<x-layouts.admin title="Finance Dashboard">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Finance Dashboard</h1>
            @can('finance.journal')
                <x-ui.button href="{{ route('admin.finance.journal') }}" variant="outline" size="sm">Jurnal</x-ui.button>
            @endcan
            @can('manage_accounting_periods')
                <x-ui.button href="{{ route('admin.finance.periods.index') }}" variant="outline" size="sm">Periode Akuntansi</x-ui.button>
            @endcan
            @can('finance.access')
                <x-ui.button href="{{ route('admin.finance.accounts') }}" variant="outline" size="sm">Akun</x-ui.button>
            @endcan
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-ui.card>
            <x-slot:header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Saldo Akun</h2>
            </x-slot:header>
            <div class="space-y-3">
                @forelse($journalEntries as $entry)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ $entry->account->name ?? 'Akun tidak ditemukan' }}</span>
                        <span class="font-medium {{ ($entry->balance ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format(abs($entry->balance ?? 0), 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada data jurnal.</p>
                @endforelse
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Ringkasan</h2>
            </x-slot:header>
            <div class="space-y-3">
                @can('view_financial_reports')
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Laporan Keuangan</span>
                        <x-ui.button href="{{ route('admin.reports.revenue') }}" variant="ghost" size="sm">Lihat</x-ui.button>
                    </div>
                @endcan
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Neraca Saldo</span>
                    <x-ui.button href="{{ route('admin.finance.trial-balance') }}" variant="ghost" size="sm">Lihat</x-ui.button>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Laba Rugi</span>
                    <x-ui.button href="{{ route('admin.finance.income-statement') }}" variant="ghost" size="sm">Lihat</x-ui.button>
                </div>
            </div>
        </x-ui.card>
    </div>
</x-layouts.admin>
