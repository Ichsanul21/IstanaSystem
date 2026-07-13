<x-layouts.admin title="Chart of Accounts">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chart of Accounts</h1>
            <x-ui.button href="#" variant="primary" size="sm">+ Tambah Akun</x-ui.button>
        </div>
    </x-slot:header>

    @php
        $typeLabels = [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Modal',
            'revenue' => 'Pendapatan',
            'expense' => 'Beban',
        ];
        $grouped = $accounts->groupBy('type');
    @endphp

    <div class="space-y-6">
        @forelse($grouped as $type => $typeAccounts)
            <x-ui.card>
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $typeLabels[$type] ?? ucfirst($type) }}</h3>
                            <span class="text-sm text-gray-500 dark:text-gray-400">({{ $typeAccounts->count() }} akun)</span>
                        </div>
                    </div>
                </x-slot:header>
                <x-ui.table :headers="['Kode', 'Nama Akun', 'Tipe', 'Aksi']">
                    @foreach($typeAccounts as $account)
                        <tr>
                            <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ $account->code }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $account->name }}</td>
                            <td class="px-6 py-4">
                                <x-ui.badge variant="gray" size="sm">{{ $typeLabels[$type] ?? ucfirst($type) }}</x-ui.badge>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <x-ui.button href="#" variant="ghost" size="sm">Edit</x-ui.button>
                                    <x-ui.button href="#" variant="ghost" size="sm">History</x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            </x-ui.card>
        @empty
            <x-ui.card>
                <p class="text-center text-sm text-gray-500 dark:text-gray-400 py-8">Tidak ada akun.</p>
            </x-ui.card>
        @endforelse
    </div>
</x-layouts.admin>
