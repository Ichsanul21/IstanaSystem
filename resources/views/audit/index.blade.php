<x-layouts.admin title="Audit Log">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Log</h1>
            @if(request()->anyFilled(['user_id', 'event', 'date_from', 'date_to']))
                <x-ui.button href="{{ route('admin.audit.index') }}" variant="ghost" size="sm">Clear Filter</x-ui.button>
            @endif
        </div>
    </x-slot:header>

    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('admin.audit.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-ui.label>User</x-ui.label>
                <x-ui.select name="user_id" placeholder="Semua User" :options="$users ?? []" value="{{ request('user_id') }}" />
            </div>
            <div>
                <x-ui.label>Event</x-ui.label>
                <x-ui.select name="event" placeholder="Semua Event" :options="['created' => 'Created', 'updated' => 'Updated', 'deleted' => 'Deleted', 'login' => 'Login', 'logout' => 'Logout']" value="{{ request('event') }}" />
            </div>
            <div>
                <x-ui.input type="date" name="date_from" label="Dari Tanggal" value="{{ request('date_from') }}" />
            </div>
            <div>
                <x-ui.input type="date" name="date_to" label="Sampai Tanggal" value="{{ request('date_to') }}" />
            </div>
            <div class="md:col-span-4 flex items-center gap-3">
                <x-ui.button type="submit" variant="primary" size="md">Filter</x-ui.button>
                <x-ui.button href="{{ route('admin.audit.index') }}" variant="ghost" size="md">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card>
        <x-ui.table :headers="['Timestamp', 'User', 'Event', 'Subject', 'Deskripsi', 'IP Address']">
            @forelse($logs as $log)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-primary text-white text-xs font-semibold">{{ strtoupper(substr($log->user->name ?? $log->causer_type ?? '-', 0, 1)) }}</div>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $log->user->name ?? 'System' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $ev = $log->event ?? '';
                            $evm = ['created' => 'success', 'updated' => 'warning', 'deleted' => 'danger', 'login' => 'info', 'logout' => 'gray'];
                        @endphp
                        <x-ui.badge :variant="$evm[$ev] ?? 'gray'" size="sm">{{ ucfirst($ev) }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $log->log_name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $log->description ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ $log->properties['ip'] ?? $log->properties ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada log ditemukan.</td>
                </tr>
            @endforelse
        </x-ui.table>
        @if($logs->hasPages())
            <div class="mt-4">
                <x-ui.pagination :paginator="$logs" />
            </div>
        @endif
    </x-ui.card>
</x-layouts.admin>
