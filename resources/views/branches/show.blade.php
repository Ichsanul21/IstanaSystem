<x-layouts.admin title="Detail Cabang">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Cabang: {{ $branch->name }}</h1>
            <div class="flex items-center gap-3">
                <x-ui.button href="{{ route('admin.branches.edit', $branch) }}" variant="outline" size="sm">Edit Cabang</x-ui.button>
                <x-ui.button href="{{ route('admin.branches.index') }}" variant="ghost" size="sm">Kembali</x-ui.button>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Cabang</h2>
                </x-slot:header>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Kode</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $branch->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Workshop</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $branch->workshop->name ?? '-' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Alamat</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $branch->address ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Telepon</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $branch->phone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                        <dd><x-ui.badge :variant="$branch->is_active ? 'success' : 'danger'">{{ $branch->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></dd>
                    </div>
                </dl>
            </x-ui.card>
        </div>
    </div>
</x-layouts.admin>
