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
                            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </x-slot:header>
        </x-ui.card>

        @if($showForm ?? false)
            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ isset($pricing) ? 'Edit Harga' : 'Tambah Harga Baru' }}</h2>
                </x-slot:header>
                <form method="POST" action="{{ isset($pricing) ? route('admin.services.pricing.update', $pricing) : route('admin.services.pricing.store') }}">
                    @csrf
                    @if(isset($pricing)) @method('PUT') @endif
                    <input type="hidden" name="branch_id" value="{{ $branchId ?? $pricing->branch_id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.select name="service_id" label="Layanan" :options="$services->map(fn($s) => ['value' => $s->id, 'label' => $s->name])->toArray()" required />
                        <x-ui.input name="price" label="Harga" type="number" step="1" min="0" value="{{ old('price', $pricing->price ?? '') }}" required />
                        <x-ui.input name="min_weight" label="Berat Minimal (kg)" type="number" step="0.5" min="0" value="{{ old('min_weight', $pricing->min_weight ?? '') }}" />
                        <x-ui.input name="max_weight" label="Berat Maksimal (kg)" type="number" step="0.5" min="0" value="{{ old('max_weight', $pricing->max_weight ?? '') }}" />
                        <x-ui.input name="estimated_days" label="Estimasi (hari)" type="number" min="1" value="{{ old('estimated_days', $pricing->estimated_days ?? '') }}" />
                        <x-ui.select name="is_active" label="Status" :options="[['value' => 1, 'label' => 'Aktif'], ['value' => 0, 'label' => 'Nonaktif']]" value="{{ old('is_active', $pricing->is_active ?? true) ? 1 : 0 }}" />
                    </div>
                    <div class="flex justify-end space-x-3 mt-4">
                        <x-ui.button type="button" variant="ghost" onclick="window.location='{{ route('admin.services.pricing.index', ['branch_id' => $branchId]) }}'">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary">{{ isset($pricing) ? 'Update' : 'Simpan' }}</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        @endif

        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Harga</h2>
                    <x-ui.button href="{{ route('admin.services.pricing.create', ['branch_id' => $branchId]) }}" variant="primary" size="sm">+ Tambah Harga</x-ui.button>
                </div>
            </x-slot:header>

            @forelse($pricings as $pricingItem)
                <div class="border-b border-gray-200 dark:border-gray-700 py-4 last:border-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $pricingItem->service->name ?? '-' }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Cabang: {{ $pricingItem->branch->name ?? '-' }} |
                                Berat: {{ $pricingItem->min_weight ?? 0 }} - {{ $pricingItem->max_weight ?? '∞' }} kg |
                                Estimasi: {{ $pricingItem->estimated_days ?? '-' }} hari
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-semibold text-primary">Rp {{ number_format($pricingItem->price, 0, ',', '.') }}</span>
                            <x-ui.button href="{{ route('admin.services.pricing.edit', $pricingItem) }}" variant="ghost" size="sm">Edit</x-ui.button>
                            <form method="POST" action="{{ route('admin.services.pricing.destroy', $pricingItem) }}" onsubmit="return confirm('Hapus harga layanan ini?')" class="inline">
                                @csrf @method('DELETE')
                                <x-ui.button type="submit" variant="danger" size="sm">Hapus</x-ui.button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 py-4">Belum ada harga layanan untuk cabang ini.</p>
            @endforelse

            @if($pricings->hasPages())
                <div class="mt-4">
                    <x-ui.pagination :paginator="$pricings" />
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.admin>
