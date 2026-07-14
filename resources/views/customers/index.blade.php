<x-layouts.admin title="Pelanggan">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pelanggan</h1>
            <div class="flex items-center gap-3">
                @can('customer.create')
                <x-ui.button href="{{ route('admin.customers.create') }}" variant="primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Tambah Pelanggan
                </x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <x-ui.card>
            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="w-64">
                    <x-ui.input name="search" label="Cari" placeholder="Nama, telepon, atau kode..." :value="request('search')" />
                </div>
                <div class="w-48">
                    <x-ui.select name="membership_tier" label="Tier Member" :options="$membershipTiers" placeholder="Semua Tier" />
                </div>
                <x-ui.button type="submit" variant="primary" size="md">Cari</x-ui.button>
                @if(request()->anyFilled(['search', 'membership_tier']))
                    <x-ui.button href="{{ route('admin.customers.index') }}" variant="ghost" size="md">Reset</x-ui.button>
                @endif
            </form>
        </x-ui.card>

        <x-ui.card padding="none">
            <x-ui.table :headers="[
                ['label' => 'Kode', 'sortable' => true],
                ['label' => 'Nama', 'sortable' => true],
                ['label' => 'Telepon'],
                ['label' => 'Tier Member'],
                ['label' => 'Poin'],
                ['label' => 'Total Order'],
                ['label' => 'Order Terakhir'],
                ['label' => 'Aksi'],
            ]">
                @forelse($customers as $customer)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $customer->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-primary hover:underline">{{ $customer->name }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $customer->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($customer->membershipTier)
                                <x-ui.badge variant="primary">{{ $customer->membershipTier->name }}</x-ui.badge>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ number_format($customer->total_points) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $customer->orders_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $customer->orders->first()?->created_at?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <x-ui.button href="{{ route('admin.customers.show', $customer) }}" variant="ghost" size="sm">Detail</x-ui.button>
                                @can('customer.update')
                                <x-ui.button href="{{ route('admin.customers.edit', $customer) }}" variant="ghost" size="sm">Edit</x-ui.button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada pelanggan ditemukan.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        @if($customers->hasPages())
            <x-ui.pagination :paginator="$customers" />
        @endif
    </div>
</x-layouts.admin>