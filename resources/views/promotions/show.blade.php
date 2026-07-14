<x-layouts.admin title="{{ $promotion->name }}">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.promotions.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $promotion->name }}</h1>
                @if($promotion->is_active)
                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                @else
                    <x-ui.badge variant="danger">Nonaktif</x-ui.badge>
                @endif
            </div>
            @can('promotion.update')
            <x-ui.button href="{{ route('admin.promotions.edit', $promotion) }}" variant="outline">Edit</x-ui.button>
            @endcan
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <x-ui.card>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Informasi Promosi</p>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kode</p>
                                <p class="text-sm font-mono font-medium text-gray-900 dark:text-white">{{ $promotion->code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tipe</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $promotion->type->label() }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nilai</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if($promotion->type->isPercentage())
                                        {{ $promotion->value }}%
                                    @elseif($promotion->type->isFixed())
                                        Rp {{ number_format($promotion->value) }}
                                    @else
                                        {{ $promotion->value }}
                                    @endif
                                </p>
                            </div>
                            @if($promotion->min_order_amount)
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Min. Pembelian</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($promotion->min_order_amount) }}</p>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Periode</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $promotion->start_date?->format('d/m/Y') }} - {{ $promotion->end_date?->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        @if($promotion->description)
                            <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Deskripsi</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $promotion->description }}</p>
                            </div>
                        @endif
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <x-slot:header>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Statistik Penggunaan</h3>
                    </x-slot:header>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Digunakan</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($promotion->usages->count()) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Diskon</span>
                            <span class="text-lg font-bold text-primary">Rp {{ number_format($promotion->usages->sum('discount_amount')) }}</span>
                        </div>
                    </div>
                </x-ui.card>
            </div>

            <div class="lg:col-span-2">
                <x-ui.card>
                    <x-slot:header>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cabang Tertaut</h3>
                    </x-slot:header>
                    <x-ui.table :headers="[['label' => 'Nama Cabang'], ['label' => 'Kota']]">
                        @forelse($promotion->branches as $branch)
                            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $branch->name }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $branch->city ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada cabang yang tertaut.</td>
                            </tr>
                        @endforelse
                    </x-ui.table>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-layouts.admin>