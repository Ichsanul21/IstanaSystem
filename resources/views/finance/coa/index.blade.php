<x-layouts.admin title="Chart of Accounts">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chart of Accounts</h1>
            <x-ui.button href="{{ route('admin.finance.coa.create') }}" variant="primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Akun
            </x-ui.button>
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <x-ui.card padding="none">
            <x-ui.table :headers="['Code', 'Nama', 'Tipe', 'Saldo Normal', 'Status', 'Aksi']">
                @forelse($accounts as $account)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-400">{{ $account->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $account->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($account->type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($account->normal_balance ?? 'debit') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($account->is_active)
                                <x-ui.badge variant="success" size="sm">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger" size="sm">Nonaktif</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <x-ui.button href="{{ route('admin.finance.coa.edit', $account) }}" variant="ghost" size="sm">Edit</x-ui.button>
                                <form method="POST" action="{{ route('admin.finance.coa.destroy', $account) }}" x-on:submit="return confirm('Hapus akun ini?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="submit" variant="ghost" size="sm">Hapus</x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada akun.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        @if($accounts->hasPages())
            <x-ui.pagination :paginator="$accounts" />
        @endif
    </div>
</x-layouts.admin>
