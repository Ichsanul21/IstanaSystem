<x-layouts.admin title="Activity Logs">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Activity Logs</h1>
            @can('view_activity_logs')
                <div class="flex items-center gap-2">
                    <x-ui.button href="{{ route('admin.audit.export', ['export' => 'excel']) }}" variant="outline" size="sm">Export Excel</x-ui.button>
                    <x-ui.button href="{{ route('admin.audit.export', ['export' => 'excel']) }}" variant="outline" size="sm">Export PDF</x-ui.button>
                </div>
            @endcan
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <x-ui.card>
            <form method="GET" action="{{ route('admin.audit.index') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <x-ui.input name="date_from" label="Dari Tanggal" type="date" :value="request('date_from')" />
                </div>
                <div>
                    <x-ui.input name="date_to" label="Sampai Tanggal" type="date" :value="request('date_to')" />
                </div>
                <div class="w-48">
                    <x-ui.select name="user_id" label="User" :options="$users ?? []" placeholder="Semua User" />
                </div>
                <div class="w-48">
                    @php $eventTypes = ['created' => 'Created', 'updated' => 'Updated', 'deleted' => 'Deleted', 'restored' => 'Restored', 'login' => 'Login', 'logout' => 'Logout']; @endphp
                    <x-ui.select name="event" label="Event" :options="$eventTypes" placeholder="Semua Event" />
                </div>
                <x-ui.button type="submit" variant="primary" size="md">Filter</x-ui.button>
                @if(request()->anyFilled(['date_from', 'date_to', 'user_id', 'event']))
                    <x-ui.button href="{{ route('admin.audit.index') }}" variant="ghost" size="md">Reset</x-ui.button>
                @endif
            </form>
        </x-ui.card>

        <x-ui.card padding="none">
            <x-ui.table :headers="['Tanggal', 'User', 'Event', 'Target', 'Detail', 'IP Address']">
                @forelse($logs as $log)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-ui.badge :variant="$log->event === 'deleted' ? 'danger' : ($log->event === 'created' ? 'success' : 'info')" size="sm">{{ ucfirst($log->event) }}</x-ui.badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $log->auditable_type ? class_basename($log->auditable_type) . ' #' . $log->auditable_id : '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $log->description ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada log.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        @if($logs->hasPages())
            <x-ui.pagination :paginator="$logs" />
        @endif
    </div>
</x-layouts.admin>
