<x-layouts.admin title="Backup Management">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Backup Management</h1>
            <form method="POST" action="{{ route('admin.backup.create') }}" x-data="{ loading: false }" x-on:submit="loading = true">
                @csrf
                <x-ui.button type="submit" variant="primary" :loading="false" x-bind:loading="loading">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                    Run Backup Now
                </x-ui.button>
            </form>
        </div>
    </x-slot:header>

    <div class="space-y-6">
        @if($lastBackup)
            <x-ui.card>
                <x-slot:header>
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Last Backup</h3>
                    </div>
                </x-slot:header>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">File</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $lastBackup->filename ?? $lastBackup['filename'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Size</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $lastBackup->size ?? $lastBackup['size'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $lastBackup->created_at ?? $lastBackup['created_at'] ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card>
        @endif

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Available Backups</h3>
            </x-slot:header>
            <x-ui.table :headers="['File', 'Size', 'Date', 'Aksi']">
                @forelse($backups as $backup)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">{{ $backup->filename ?? $backup['filename'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $backup->size ?? $backup['size'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $backup->created_at ?? $backup['created_at'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <x-ui.button href="{{ route('admin.backup.download', $backup->filename ?? $backup['filename']) }}" variant="ghost" size="sm">Download</x-ui.button>
                                <form method="POST" action="{{ route('admin.backup.destroy', $backup->filename ?? $backup['filename']) }}" x-on:submit="return confirm('Hapus backup ini?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="submit" variant="ghost" size="sm">Hapus</x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada backup.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>
    </div>
</x-layouts.admin>
