@props(['orderStatusDistribution' => [], 'topCustomers' => []])

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <x-dashboard.partials.chart-card title="Distribusi Status Pesanan" id="orderStatusChart" />
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-bold">Pelanggan Teratas</h3>
        </x-slot:header>
        @if(count($topCustomers) > 0)
        <div class="space-y-4">
            @foreach($topCustomers as $i => $customer)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-sm font-bold">{{ $i + 1 }}</span>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $customer['name'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer['order_count'] }} pesanan</p>
                    </div>
                </div>
                <p class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($customer['total_spent'], 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">Belum ada data pelanggan.</p>
        @endif
    </x-ui.card>
</div>

<div class="mt-6">
    <x-dashboard.partials.recent-orders :orders="($recentOrders ?? [])" />
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusData = @json($orderStatusDistribution);

    new Chart(document.getElementById('orderStatusChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(statusData),
            datasets: [{
                label: 'Jumlah Pesanan',
                data: Object.values(statusData),
                backgroundColor: ['#6B7280', '#F59E0B', '#3B82F6', '#10B981', '#EF4444'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});
</script>
@endpush
