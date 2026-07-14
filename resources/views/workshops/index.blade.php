<x-layouts.admin title="Workshop">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Workshop</h1>
            <x-ui.button href="{{ route('admin.workshops.create') }}" variant="primary">+ Tambah Workshop</x-ui.button>
        </div>
    </x-slot:header>

    <x-ui.card>
        <x-ui.table :headers="['Kode', 'Nama', 'Alamat', 'Telepon', 'Status', 'Aksi']">
            @forelse($workshops as $workshop)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ $workshop->code }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-white text-sm font-bold">{{ strtoupper(substr($workshop->name, 0, 2)) }}</div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $workshop->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $workshop->address ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $workshop->phone ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <x-ui.badge :variant="$workshop->is_active ? 'success' : 'danger'" size="sm">{{ $workshop->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <x-ui.button href="{{ route('admin.workshops.edit', $workshop) }}" variant="ghost" size="sm">Edit</x-ui.button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada data workshop.</td>
                </tr>
            @endforelse
        </x-ui.table>
        @if(method_exists($workshops, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $workshops->links() }}
            </div>
        @endif
    </x-ui.card>
</x-layouts.admin>
