<x-layouts.admin title="Tambah User">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah User</h1>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-primary hover:text-primary-dark">Kembali</a>
        </div>
    </x-slot:header>

    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input name="name" label="Nama Lengkap" required placeholder="Masukkan nama lengkap" />
                    <x-ui.input name="email" label="Email" type="email" required placeholder="user@example.com" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input name="password" label="Password" type="password" required placeholder="Minimal 8 karakter" />
                    <x-ui.input name="password_confirmation" label="Konfirmasi Password" type="password" required placeholder="Ulangi password" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.select name="role" label="Role" required :options="$roles->pluck('name', 'name')->prepend('Pilih Role', '')->toArray()" />
                    <x-ui.select name="branch_id" label="Cabang" required :options="$branches->pluck('name', 'id')->prepend('Pilih Cabang', '')->toArray()" />
                </div>
                <div>
                    <x-ui.label for="is_active">Status</x-ui.label>
                    <div class="mt-1 flex items-center gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="is_active" value="1" checked class="text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="is_active" value="0" class="text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Nonaktif</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-ui.button type="button" variant="ghost">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.admin>