<x-layouts.admin title="{{ $customer->name }}">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customer->name }}</h1>
                @if($customer->membershipTier)
                    <x-ui.badge variant="primary">{{ $customer->membershipTier->name }}</x-ui.badge>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <x-ui.button href="{{ route('admin.customers.edit', $customer) }}" variant="outline">Edit</x-ui.button>
                <x-ui.button x-on:click="$dispatch('open-modal', 'add-points-modal')" variant="primary">Tambah Poin</x-ui.button>
            </div>
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <x-ui.tabs :tabs="[
            ['id' => 'info', 'label' => 'Info', 'active' => true],
            ['id' => 'orders', 'label' => 'Pesanan'],
            ['id' => 'points', 'label' => 'Poin'],
            ['id' => 'notes', 'label' => 'Catatan'],
        ]">
            <x-slot:tab-info>
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="lg:col-span-1">
                        <x-ui.card>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Informasi Pelanggan</p>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Kode</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $customer->code }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Telepon</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $customer->phone ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $customer->email ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Alamat</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $customer->address ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Tier Member</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $customer->membershipTier?->name ?? '-' }}</p>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Poin</p>
                                        <p class="text-2xl font-bold text-primary">{{ number_format($customer->total_points) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Belanja</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format($customer->total_purchase, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </x-ui.card>
                    </div>
                    <div class="lg:col-span-2">
                        <x-ui.card>
                            <x-slot:header>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ringkasan</h3>
                            </x-slot:header>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-primary">{{ $ordersCount ?? $customer->orders()->count() }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Pesanan</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-success">{{ number_format($customer->total_points) }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Poin Aktif</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-warning">{{ $customer->membershipTier?->name ?? 'Regular' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tier Saat Ini</p>
                                </div>
                            </div>
                        </x-ui.card>
                    </div>
                </div>
            </x-slot:tab-info>

            <x-slot:tab-orders>
                <x-ui.card>
                    <x-slot:header>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Pesanan</h3>
                    </x-slot:header>
                    <x-ui.table :headers="[['label' => 'Kode'], ['label' => 'Tanggal'], ['label' => 'Status'], ['label' => 'Total'], ['label' => 'Aksi']]">
                        @forelse($recentOrders as $order)
                            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $order->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @php $statusColors = ['draft'=>'gray','pending'=>'primary','processing'=>'warning','completed'=>'success','cancelled'=>'danger']; @endphp
                                    <x-ui.badge :variant="$statusColors[$order->status] ?? 'gray'">{{ ucfirst($order->status) }}</x-ui.badge>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm">
                                    <x-ui.button href="{{ route('admin.orders.show', $order) }}" variant="ghost" size="sm">Detail</x-ui.button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada pesanan.</td>
                            </tr>
                        @endforelse
                    </x-ui.table>
                </x-ui.card>
            </x-slot:tab-orders>

            <x-slot:tab-points>
                <x-ui.card>
                    <x-slot:header>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Poin</h3>
                            <x-ui.button x-on:click="$dispatch('open-modal', 'add-points-modal')" variant="primary" size="sm">Tambah Poin</x-ui.button>
                        </div>
                    </x-slot:header>
                    <x-ui.table :headers="[['label' => 'Tanggal'], ['label' => 'Tipe'], ['label' => 'Deskripsi'], ['label' => 'Jumlah'], ['label' => 'Kadaluarsa']]">
                        @forelse($recentPoints as $point)
                            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $point->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @php
                                        $typeLabels = ['earn' => 'Earn', 'redeem' => 'Redeem', 'expire' => 'Expire', 'adjust' => 'Adjust'];
                                        $typeVariants = ['earn' => 'success', 'redeem' => 'warning', 'expire' => 'danger', 'adjust' => 'primary'];
                                    @endphp
                                    <x-ui.badge :variant="$typeVariants[$point->type] ?? 'gray'">{{ $typeLabels[$point->type] ?? $point->type }}</x-ui.badge>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $point->reference ?? '-' }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium {{ $point->points >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $point->points >= 0 ? '+' : '' }}{{ number_format($point->points) }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $point->expires_at ? $point->expires_at->format('d/m/Y') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat poin.</td>
                            </tr>
                        @endforelse
                    </x-ui.table>
                </x-ui.card>
            </x-slot:tab-points>

            <x-slot:tab-notes>
                <x-ui.card>
                    <x-slot:header>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Catatan</h3>
                    </x-slot:header>
                    <form method="POST" action="{{ route('admin.customers.notes', $customer) }}" class="space-y-4">
                        @csrf
                        <x-ui.textarea name="notes" label="Catatan Pelanggan" rows="4">{{ $customer->notes }}</x-ui.textarea>
                        <div class="flex items-center justify-end gap-3">
                            <x-ui.button type="submit" variant="primary">Simpan Catatan</x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </x-slot:tab-notes>
        </x-ui.tabs>
    </div>

    <x-ui.modal name="add-points-modal" title="Tambah / Adjust Poin" maxWidth="md">
        <form method="POST" action="{{ route('admin.customers.points', $customer) }}" class="space-y-4">
            @csrf
            <x-ui.input name="points" label="Jumlah Poin" type="number" required help="Gunakan angka positif untuk menambah, negatif untuk mengurangi." />
            <x-ui.textarea name="reason" label="Deskripsi" rows="2" required />
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</x-layouts.admin>
