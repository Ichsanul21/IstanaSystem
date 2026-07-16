<x-layouts.admin title="Edit Cabang">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Cabang</h1>
            <a href="{{ route('admin.branches.index') }}" class="text-sm text-primary hover:text-primary-dark">Kembali</a>
        </div>
    </x-slot:header>

    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.branches.update', $branch) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input name="name" label="Nama Cabang" :value="old('name', $branch->name)" required />
                    <x-ui.input name="code" label="Kode Cabang" :value="old('code', $branch->code)" required />
                </div>
                <x-ui.textarea name="address" label="Alamat" rows="2">{{ old('address', $branch->address) }}</x-ui.textarea>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input name="phone" label="Telepon" :value="old('phone', $branch->phone)" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-ui.input name="opening_time" label="Jam Buka" type="time" :value="old('opening_time', $branch->opening_time)" />
                    <x-ui.input name="closing_time" label="Jam Tutup" type="time" :value="old('closing_time', $branch->closing_time)" />
                    <x-ui.input name="daily_capacity" label="Kapasitas Harian" type="number" min="1" :value="old('daily_capacity', $branch->daily_capacity)" />
                </div>
                @if(isset($workshops))
                <x-ui.select name="workshop_id" label="Workshop" :options="$workshops->pluck('name', 'id')->prepend('Pilih Workshop', '')->toArray()" :value="old('workshop_id', $branch->workshop_id)" />
                @endif
                <div>
                    <x-ui.label for="is_active">Status</x-ui.label>
                    <div class="mt-1 flex items-center gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="is_active" value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }} class="text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="is_active" value="0" {{ !old('is_active', $branch->is_active) ? 'checked' : '' }} class="text-primary focus:ring-primary">
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