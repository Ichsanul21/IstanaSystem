<x-layouts.admin title="Accounting Periods">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Accounting Periods</h1>
            <x-ui.button href="{{ route('admin.finance.periods.create') }}" variant="primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Periode
            </x-ui.button>
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <x-ui.card padding="none">
            <x-ui.table :headers="['Nama', 'Start Date', 'End Date', 'Status', 'Aksi']">
                @forelse($periods as $period)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $period->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $period->start_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $period->end_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($period->is_closed)
                                <x-ui.badge variant="danger" size="sm">Closed</x-ui.badge>
                            @else
                                <x-ui.badge variant="success" size="sm">Open</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                @unless($period->is_closed)
                                    <form method="POST" action="{{ route('admin.finance.periods.close', $period) }}" x-on:submit="return confirm('Tutup periode ini?')">
                                        @csrf
                                        <x-ui.button type="submit" variant="ghost" size="sm">Close</x-ui.button>
                                    </form>
                                @endunless
                                <x-ui.button href="{{ route('admin.finance.periods.edit', $period) }}" variant="ghost" size="sm">Edit</x-ui.button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada periode.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        @if($periods->hasPages())
            <x-ui.pagination :paginator="$periods" />
        @endif
    </div>
</x-layouts.admin>
