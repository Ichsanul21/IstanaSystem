<x-layouts.admin title="Layanan">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Layanan</h1>
            @can('create_services')
            <x-ui.button href="{{ route('admin.services.create') }}" variant="primary">+ Tambah Layanan</x-ui.button>
            @endcan
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <x-ui.card padding="none">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Layanan</h2>
                    <form method="GET" class="flex gap-2">
                        <x-ui.input type="text" name="search" placeholder="Cari layanan..." value="{{ request('search') }}" class="max-w-xs" />
                        <x-ui.button type="submit" variant="primary" size="sm">Cari</x-ui.button>
                        @if(request('search'))
                        <x-ui.button href="{{ route('admin.services.index') }}" variant="ghost" size="sm">Reset</x-ui.button>
                        @endif
                    </form>
                </div>
            </div>
            <x-ui.table :headers="['Kode', 'Nama', 'Satuan', 'Status', 'Aksi']">
                @forelse($services as $service)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-medium text-gray-900 dark:text-white">{{ $service->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $service->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $service->unit }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($service->is_active)
                                <x-ui.badge variant="success">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger">Nonaktif</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                @can('edit_services')
                                <x-ui.button href="{{ route('admin.services.edit', $service) }}" variant="ghost" size="sm">Edit</x-ui.button>
                                <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Hapus layanan ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="submit" size="sm" variant="ghost" class="text-red-600 hover:text-red-700">Hapus</x-ui.button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada layanan.</td>
                    </tr>
                @endforelse
            </x-ui.table>
            @if($services->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <x-ui.pagination :paginator="$services" />
                </div>
            @endif
        </x-ui.card>

        @can('edit_service_pricing')
        <x-ui.card>
            <x-slot:header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Harga per Cabang</h2>
            </x-slot:header>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Atur harga layanan untuk setiap cabang.</p>
            <x-ui.button href="{{ route('admin.services.pricing.index', ['branch_id' => currentBranchId()]) }}" variant="outline" size="sm">Kelola Harga</x-ui.button>
        </x-ui.card>
        @endcan
    </div>
</x-layouts.admin>
