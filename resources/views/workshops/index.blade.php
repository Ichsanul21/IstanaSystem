<x-layouts.admin title="Workshop">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Workshop</h1>
            <x-ui.button href="{{ route('admin.workshops.create') }}" variant="primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Workshop
            </x-ui.button>
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <x-ui.card>
            <form method="GET" action="{{ route('admin.workshops.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="w-64">
                    <x-ui.input name="search" label="Cari" placeholder="Nama workshop..." :value="request('search')" />
                </div>
                <x-ui.button type="submit" variant="primary" size="md">Filter</x-ui.button>
                @if(request('search'))
                    <x-ui.button href="{{ route('admin.workshops.index') }}" variant="ghost" size="md">Reset</x-ui.button>
                @endif
            </form>
        </x-ui.card>

        <x-ui.card padding="none">
            <x-ui.table :headers="['#', 'Nama', 'Alamat', 'Jumlah Cabang', 'Aksi']">
                @forelse($workshops as $workshop)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $workshop->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $workshop->address ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $workshop->branches_count ?? $workshop->branches?->count() ?? 0 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <x-ui.button href="{{ route('admin.workshops.edit', $workshop) }}" variant="ghost" size="sm">Edit</x-ui.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada workshop.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        @if($workshops->hasPages())
            <x-ui.pagination :paginator="$workshops" />
        @endif
    </div>
</x-layouts.admin>
