<x-layouts.admin title="Manajemen User">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen User</h1>
            <x-ui.button href="{{ route('admin.users.create') }}" variant="primary">+ Tambah User</x-ui.button>
        </div>
    </x-slot:header>

    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="w-56">
                <x-ui.input type="text" name="search" placeholder="Cari nama atau email..." value="{{ request('search') }}" />
            </div>
            <x-ui.button type="submit" variant="primary" size="md">Filter</x-ui.button>
            @if(request()->has('search'))
                <x-ui.button href="{{ route('admin.users.index') }}" variant="ghost" size="md">Reset</x-ui.button>
            @endif
        </form>
    </x-ui.card>

    <x-ui.card>
        <x-ui.table :headers="['Nama', 'Email', 'Role', 'Cabang', 'Aksi']">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-white text-sm font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @foreach($user->roles as $role)
                            <x-ui.badge variant="primary" size="sm">{{ $role->name }}</x-ui.badge>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->branch->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <x-ui.button href="{{ route('admin.users.edit', $user) }}" variant="ghost" size="sm">Edit</x-ui.button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada user ditemukan.</td>
                </tr>
            @endforelse
        </x-ui.table>
        @if($users->hasPages())
            <div class="mt-4">
                <x-ui.pagination :paginator="$users" />
            </div>
        @endif
    </x-ui.card>
</x-layouts.admin>
