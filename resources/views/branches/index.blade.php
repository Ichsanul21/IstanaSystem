<x-layouts.admin title="Cabang">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Cabang</h1>
            <x-ui.button href="{{ route('admin.branches.create') }}" variant="primary">+ Tambah Cabang</x-ui.button>
        </div>
    </x-slot:header>

    <x-ui.card>
        <x-ui.table :headers="['Nama', 'Kode', 'Alamat', 'Telepon', 'Status', 'Aksi']">
            @forelse($branches as $branch)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-white text-sm font-bold">{{ strtoupper(substr($branch->name, 0, 2)) }}</div>
                            <div>
                                <a href="{{ route('admin.branches.show', $branch) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-primary">{{ $branch->name }}</a>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $branch->workshop->name ?? 'Pusat' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ $branch->code }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $branch->address ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $branch->phone ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <x-ui.badge :variant="$branch->is_active ? 'success' : 'danger'" size="sm">{{ $branch->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <x-ui.button href="{{ route('admin.branches.edit', $branch) }}" variant="ghost" size="sm">Edit</x-ui.button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada data cabang.</td>
                </tr>
            @endforelse
        </x-ui.table>
        @if(method_exists($branches, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $branches->links() }}
            </div>
        @endif
    </x-ui.card>
</x-layouts.admin>
