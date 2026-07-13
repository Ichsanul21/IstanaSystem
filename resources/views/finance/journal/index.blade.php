<x-layouts.admin title="Jurnal Transaksi">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Jurnal Transaksi</h1>
            <x-ui.button href="{{ route('admin.finance.journal.create') }}" variant="primary">+ Tambah Jurnal</x-ui.button>
        </div>
    </x-slot:header>

    <x-ui.card>
        <x-ui.table :headers="['Tanggal', 'Referensi', 'Akun', 'Debit', 'Kredit', 'Deskripsi', 'Aksi']">
            @forelse($entries as $entry)
                @foreach($entry->lines as $line)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $entry->posted_at?->format('Y-m-d') ?? $entry->created_at->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ $entry->entry_number }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $line->account->code }} {{ $line->account->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $line->debit > 0 ? 'Rp ' . number_format($line->debit, 0, ',', '.') : '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $line->credit > 0 ? 'Rp ' . number_format($line->credit, 0, ',', '.') : '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $line->description ?? $entry->description }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1">
                            <x-ui.button href="#" variant="icon" size="sm">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            </x-ui.button>
                            <x-ui.button href="#" variant="icon" size="sm">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                            </x-ui.button>
                            <x-ui.button href="#" variant="icon" size="sm">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </x-ui.button>
                        </div>
                    </td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada jurnal.</td>
                </tr>
            @endforelse
        </x-ui.table>
        @if(method_exists($entries, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $entries->links() }}
            </div>
        @endif
    </x-ui.card>
</x-layouts.admin>
