<x-layouts.admin title="Expenses">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Expenses</h1>
            @can('finance.expense')
            <x-ui.button href="{{ route('admin.finance.expenses.create') }}" variant="primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Expense
            </x-ui.button>
            @endcan
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <x-ui.card>
            <form method="GET" action="{{ route('admin.finance.expenses.index') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <x-ui.input name="date_from" label="Dari Tanggal" type="date" :value="request('date_from')" />
                </div>
                <div>
                    <x-ui.input name="date_to" label="Sampai Tanggal" type="date" :value="request('date_to')" />
                </div>
                <div class="w-48">
                    <x-ui.select name="category" label="Kategori" :options="$categories ?? []" placeholder="Semua Kategori" />
                </div>
                <x-ui.button type="submit" variant="primary" size="md">Filter</x-ui.button>
                @if(request()->anyFilled(['date_from', 'date_to', 'category']))
                    <x-ui.button href="{{ route('admin.finance.expenses.index') }}" variant="ghost" size="md">Reset</x-ui.button>
                @endif
            </form>
        </x-ui.card>

        <x-ui.card padding="none">
            <x-ui.table :headers="['Tanggal', 'Kategori', 'Deskripsi', 'Jumlah', 'Metode', 'Referensi', 'Aksi']">
                @forelse($expenses as $expense)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $expense->expense_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $expense->category }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">{{ $expense->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $expense->payment_method ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $expense->reference ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                @can('finance.expense')
                                <x-ui.button href="{{ route('admin.finance.expenses.edit', $expense) }}" variant="ghost" size="sm">Edit</x-ui.button>
                                <form method="POST" action="{{ route('admin.finance.expenses.destroy', $expense) }}" x-on:submit="return confirm('Hapus expense ini?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="submit" variant="ghost" size="sm">Hapus</x-ui.button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada expense.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        @if($expenses->hasPages())
            <x-ui.pagination :paginator="$expenses" />
        @endif
    </div>
</x-layouts.admin>
