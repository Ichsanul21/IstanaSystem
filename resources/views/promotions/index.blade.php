<x-layouts.admin title="Promosi">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Promosi</h1>
            <x-ui.button href="{{ route('admin.promotions.create') }}" variant="primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Promosi
            </x-ui.button>
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <x-ui.card padding="none">
            <x-ui.table :headers="[
                ['label' => 'Kode'],
                ['label' => 'Nama'],
                ['label' => 'Tipe'],
                ['label' => 'Nilai'],
                ['label' => 'Mulai'],
                ['label' => 'Berakhir'],
                ['label' => 'Status'],
                ['label' => 'Cabang'],
                ['label' => 'Aksi'],
            ]">
                @forelse($promotions as $promotion)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-medium text-gray-900 dark:text-white">{{ $promotion->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            <a href="{{ route('admin.promotions.show', $promotion) }}" class="text-primary hover:underline">{{ $promotion->name }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            <x-ui.badge variant="info">{{ $promotion->type->label() }}</x-ui.badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            @if($promotion->type->isPercentage())
                                {{ $promotion->value }}%
                            @elseif($promotion->type->isFixed())
                                Rp {{ number_format($promotion->value) }}
                            @else
                                {{ $promotion->value }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $promotion->start_date?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $promotion->end_date?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($promotion->is_active)
                                <x-ui.badge variant="success">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger">Nonaktif</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $promotion->branches_count }} cabang</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <x-ui.button href="{{ route('admin.promotions.show', $promotion) }}" variant="ghost" size="sm">Detail</x-ui.button>
                                <x-ui.button href="{{ route('admin.promotions.edit', $promotion) }}" variant="ghost" size="sm">Edit</x-ui.button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada promosi ditemukan.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        @if($promotions->hasPages())
            <x-ui.pagination :paginator="$promotions" />
        @endif
    </div>
</x-layouts.admin>