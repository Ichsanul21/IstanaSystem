@php
$tabs = [
    ['id' => 'pendapatan', 'label' => 'Pendapatan'],
    ['id' => 'operasional', 'label' => 'Operasional'],
    ['id' => 'produksi', 'label' => 'Produksi'],
    ['id' => 'keuangan', 'label' => 'Keuangan'],
    ['id' => 'inventory', 'label' => 'Inventory'],
];

$periods = [
    'today' => 'Hari Ini',
    'this_week' => 'Minggu Ini',
    'this_month' => 'Bulan Ini',
    'custom' => 'Kustom',
];

$selectedPeriod = request('period', $dateFrom && $dateTo ? 'custom' : 'today');
@endphp

@push('styles')
<style>[x-cloak] { display: none !important; }</style>
@endpush

<x-layouts.admin title="Dashboard">
    <x-slot:header>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-3" x-data="dateRange()">
                <form method="GET" action="{{ route('admin.dashboard') }}" x-ref="filterForm" class="flex items-center gap-2">
                    @if($branches->isNotEmpty())
                    <select name="branch_id" x-on:change="$refs.filterForm.submit()" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2 focus:ring-primary focus:border-primary">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(request('branch_id', $branchId) == $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @endif
                    <select name="period" x-model="period" x-on:change="onPeriodChange()" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2 focus:ring-primary focus:border-primary">
                        @foreach($periods as $val => $label)
                        <option value="{{ $val }}" @selected($selectedPeriod === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div x-show="period === 'custom'" x-cloak class="flex items-center gap-2">
                        <input type="date" name="date_from" x-model="dateFrom" value="{{ $dateFrom ?? '' }}" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2 focus:ring-primary focus:border-primary">
                        <span class="text-gray-500">-</span>
                        <input type="date" name="date_to" x-model="dateTo" value="{{ $dateTo ?? '' }}" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2 focus:ring-primary focus:border-primary">
                    </div>
                    <input type="hidden" name="tab" value="{{ $activeTab }}">
                    <button type="submit" class="rounded-lg bg-primary text-white px-4 py-2 text-sm font-medium hover:bg-primary-dark transition-colors">Terapkan</button>
                </form>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-dashboard.partials.metric-card
            label="Total Pesanan"
            :value="number_format($metrics['totalOrders'] ?? 0, 0, ',', '.')"
            icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'
        />
        <x-dashboard.partials.metric-card
            label="Total Pendapatan"
            value="Rp {{ number_format($metrics['totalRevenue'] ?? 0, 0, ',', '.') }}"
            icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        />
        <x-dashboard.partials.metric-card
            label="Pesanan Tertunda"
            :value="number_format($metrics['pendingOrders'] ?? 0, 0, ',', '.')"
            icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-8.69 3.56a1 1 0 00-.74.02l.98-4.27s.39-1.35-.23-2.12C2.08 11.2 1 9.68 1 9.68l5.1-3.11s2.07 1.1 3.34 1.22c1.28.13 2.53-.64 2.53-.64l6.12 2.7s.53 2.33.09 3.69c-.44 1.36-1.53 2.39-1.53 2.39l-4.7-1.97s-.78.33-1.53.11z"/></svg>'
        />
        <x-dashboard.partials.metric-card
            label="Total Pelanggan"
            :value="number_format($metrics['totalCustomers'] ?? 0, 0, ',', '.')"
            icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>'
        />
    </div>

    <x-ui.tabs :tabs="$tabs" :active="$activeTab">
        <div x-show="activeTab === 'pendapatan'" x-cloak>
            @include('dashboard.tabs.pendapatan', [
                'revenueTrend' => $revenueTrend,
                'revenueByService' => $revenueByService,
                'paymentMethods' => $paymentMethods,
            ])
        </div>

        <div x-show="activeTab === 'operasional'" x-cloak>
            @include('dashboard.tabs.operasional', [
                'orderStatusDistribution' => $orderStatusDistribution,
                'topCustomers' => $topCustomers,
                'recentOrders' => $recentOrders,
            ])
        </div>

        <div x-show="activeTab === 'produksi'" x-cloak>
            @include('dashboard.tabs.produksi', [
                'queuePerStatus' => $queuePerStatus,
                'itemsInProduction' => $itemsInProduction,
                'avgProcessingTime' => $avgProcessingTime,
            ])
        </div>

        @can('finance.read')
        <div x-show="activeTab === 'keuangan'" x-cloak>
            @include('dashboard.tabs.keuangan', [
                'revenueVsExpense' => $revenueVsExpense,
                'profitMargin' => $profitMargin,
                'monthlyTrend' => $monthlyTrend,
            ])
        </div>
        @endcan

        <div x-show="activeTab === 'inventory'" x-cloak>
            @include('dashboard.tabs.inventory', [
                'stockValue' => $stockValue,
                'lowStockItems' => $lowStockItems,
                'stockMovement' => $stockMovement,
            ])
        </div>
    </x-ui.tabs>
</x-layouts.admin>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
function dateRange() {
    return {
        period: '{{ $selectedPeriod }}',
        dateFrom: '{{ $dateFrom ?? "" }}',
        dateTo: '{{ $dateTo ?? "" }}',
        onPeriodChange() {
            const today = new Date();
            const fmt = d => d.toISOString().split('T')[0];
            if (this.period === 'today') {
                this.dateFrom = fmt(today);
                this.dateTo = fmt(today);
            } else if (this.period === 'this_week') {
                const start = new Date(today);
                start.setDate(today.getDate() - today.getDay() + 1);
                this.dateFrom = fmt(start);
                this.dateTo = fmt(today);
            } else if (this.period === 'this_month') {
                this.dateFrom = fmt(new Date(today.getFullYear(), today.getMonth(), 1));
                this.dateTo = fmt(today);
            }
        }
    }
}
</script>
@endpush
