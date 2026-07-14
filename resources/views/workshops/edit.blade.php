<x-layouts.admin title="Edit Workshop">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Workshop</h1>
            <x-ui.button href="{{ route('admin.workshops.index') }}" variant="ghost" size="sm">Kembali</x-ui.button>
        </div>
    </x-slot:header>

    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.workshops.update', $workshop) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input name="code" label="Kode Workshop" :value="old('code', $workshop->code)" required />
                    <x-ui.input name="name" label="Nama Workshop" :value="old('name', $workshop->name)" required />
                </div>
                <x-ui.textarea name="address" label="Alamat" rows="2">{{ old('address', $workshop->address) }}</x-ui.textarea>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input name="phone" label="Telepon" :value="old('phone', $workshop->phone)" />
                </div>
                <div>
                    <x-ui.label for="is_active">Status</x-ui.label>
                    <div class="mt-1 flex items-center gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="is_active" value="1" {{ old('is_active', $workshop->is_active) ? 'checked' : '' }} class="text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="is_active" value="0" {{ !old('is_active', $workshop->is_active) ? 'checked' : '' }} class="text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Nonaktif</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-ui.button href="{{ route('admin.workshops.index') }}" variant="ghost">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary">Update</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.admin>
