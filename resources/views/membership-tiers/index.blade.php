<x-layouts.admin title="Tier Member">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tier Member</h1>
            @can('membership.create')
            <x-ui.button href="{{ route('admin.membership-tiers.create') }}" variant="primary">+ Tambah Tier</x-ui.button>
            @endcan
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <x-ui.card padding="none">
            <x-ui.table :headers="[
                ['label' => 'Nama Tier'],
                ['label' => 'Level'],
                ['label' => 'Min. Poin'],
                ['label' => 'Diskon'],
                ['label' => 'Warna'],
                ['label' => 'Status'],
                ['label' => 'Aksi'],
            ]">
                @forelse($tiers as $tier)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $tier->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ number_format($tier->min_points) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $tier->discount_percent }}%</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <span class="inline-block w-4 h-4 rounded-full" style="background-color: {{ $tier->color ?? '#6B7280' }}"></span>
                                {{ $tier->color ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-ui.badge :variant="$tier->is_active ? 'success' : 'danger'">{{ $tier->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @can('membership.update')
                                <x-ui.button href="{{ route('admin.membership-tiers.edit', $tier) }}" variant="ghost" size="sm">Edit</x-ui.button>
                                @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada tier member ditemukan.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        @if($tiers->hasPages())
            <x-ui.pagination :paginator="$tiers" />
        @endif
    </div>
</x-layouts.admin>
