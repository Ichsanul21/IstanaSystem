<x-layouts.admin title="Edit User">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit User</h1>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-primary hover:text-primary-dark">Kembali</a>
        </div>
    </x-slot:header>

    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div class="flex items-center gap-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary text-white text-xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->created_at?->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input name="name" label="Nama Lengkap" :value="old('name', $user->name)" required />
                    <x-ui.input name="email" label="Email" type="email" :value="old('email', $user->email)" required />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input name="password" label="Password Baru" type="password" placeholder="Kosongkan jika tidak diubah" />
                    <x-ui.input name="password_confirmation" label="Konfirmasi Password" type="password" placeholder="Ulangi password baru" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.select name="role" label="Role" required :options="$roles->pluck('name', 'name')->toArray()" :value="old('role', $user->roles->first()?->name ?? '')" />
                    <x-ui.select name="branch_id" label="Cabang" required :options="$branches->pluck('name', 'id')->toArray()" :value="old('branch_id', $user->branch_id)" />
                </div>
                <div>
                    <x-ui.label for="is_active">Status</x-ui.label>
                    <div class="mt-1 flex items-center gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }} class="text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="is_active" value="0" {{ !old('is_active', $user->is_active ?? true) ? 'checked' : '' }} class="text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Nonaktif</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-ui.button type="button" variant="ghost">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary">Update</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.admin>