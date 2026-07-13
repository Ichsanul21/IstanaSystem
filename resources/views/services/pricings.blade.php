<x-layouts.admin title="Harga Layanan per Cabang">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Harga Layanan per Cabang</h1>
            <x-ui.button href="{{ route('admin.services.index') }}" variant="ghost">Kembali ke Layanan</x-ui.button>
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pilih Cabang</h2>
                    <form method="GET" id="branch-form">
                        <select name="branch_id" onchange="document.getElementById('branch-form').submit()"
                            class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-300 px-4 py-2.5 focus:ring-primary focus:border-primary">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected($branch->id == $branchId)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </x-slot:header>

            @can('admin-access')
            <div class="mb-4">
                <x-ui.button href="{{ route('admin.services.pricings.create', ['branch_id' => $branchId]) }}" variant="primary" size="sm">+ Tambah Harga</x-ui.button>
            </div>
            @endcan

            @isset($showForm)
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ isset($pricing) ? 'Edit Harga Layanan' : 'Tambah Harga Layanan' }}</h3>
                <form method="POST" action="{{ isset($pricing) ? route('admin.services.pricings.update', $pricing) : route('admin.services.pricings.store') }}">
                    @csrf
                    @if(isset($pricing)) @method('PUT') @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <x-ui.select name="service_id" label="Layanan" :options="$services->pluck('name', 'id')->toArray()" :value="old('service_id', $pricing->service_id ?? '')" placeholder="Pilih Layanan" required />
                        <x-ui.select name="branch_id" label="Cabang" :options="$branches->pluck('name', 'id')->toArray()" :value="old('branch_id', $pricing->branch_id ?? $branchId)" required />
                        <x-ui.input name="price" label="Harga" type="number" :value="old('price', $pricing->price ?? '')" required placeholder="0" step="0.01" />
                        <x-ui.input name="min_weight" label="Berat Min" type="number" :value="old('min_weight', $pricing->min_weight ?? '')" placeholder="0" step="0.01" />
                        <x-ui.input name="max_weight" label="Berat Maks" type="number" :value="old('max_weight', $pricing->max_weight ?? '')" placeholder="0" step="0.01" />
                        <x-ui.input name="estimated_days" label="Estimasi (hari)" type="number" :value="old('estimated_days', $pricing->estimated_days ?? '')" placeholder="0" />
                    </div>
                    <div class="mt-4">
                        <x-ui.label for="is_active">Status</x-ui.label>
                        <div class="mt-1 flex items-center gap-4">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', $pricing->is_active ?? true) ? 'checked' : '' }} class="text-primary focus:ring-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="is_active" value="0" {{ !old('is_active', $pricing->is_active ?? true) ? 'checked' : '' }} class="text-primary focus:ring-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Nonaktif</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-ui.button type="button" variant="ghost" onclick="window.location='{{ route('admin.services.pricings.index', ['branch_id' => $branchId]) }}'">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary">{{ isset($pricing) ? 'Update' : 'Simpan' }}</x-ui.button>
                    </div>
                </form>
            </div>
            @endisset

            <x-ui.table :headers="['Layanan', 'Harga', 'Berat Min', 'Berat Maks', 'Estimasi', 'Status', 'Aksi']">
                @forelse($pricings as $pricingItem)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $pricingItem->service->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($pricingItem->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $pricingItem->min_weight ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $pricingItem->max_weight ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $pricingItem->estimated_days ?? '-' }} hari</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($pricingItem->is_active)
                                <x-ui.badge variant="success">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger">Nonaktif</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <x-ui.button href="{{ route('admin.services.pricings.edit', $pricingItem) }}" variant="ghost" size="sm">Edit</x-ui.button>
                                @can('admin-access')
                                <form method="POST" action="{{ route('admin.services.pricings.destroy', $pricingItem) }}" onsubmit="return confirm('Hapus harga layanan ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="submit" size="sm" variant="ghost" class="text-red-600 hover:text-red-700">Hapus</x-ui.button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada harga untuk cabang ini.</td>
                    </tr>
                @endforelse
            </x-ui.table>
            @if($pricings->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <x-ui.pagination :paginator="$pricings" />
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.admin>
