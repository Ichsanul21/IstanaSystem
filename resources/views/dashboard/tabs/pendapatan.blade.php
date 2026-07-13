@props(['revenueTrend' => [], 'revenueByService' => [], 'paymentMethods' => []])

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <x-dashboard.partials.chart-card title="Tren Pendapatan (7 Hari)" id="revenueTrendChart" />
    <x-dashboard.partials.chart-card title="Pendapatan per Layanan" id="revenueServiceChart" />
</div>

<div class="mt-6">
    <x-dashboard.partials.chart-card title="Metode Pembayaran" id="paymentMethodChart" />
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const trendData = @json($revenueTrend);
    const serviceData = @json($revenueByService);
    const paymentData = @json($paymentMethods);

    new Chart(document.getElementById('revenueTrendChart'), {
        type: 'line',
        data: {
            labels: trendData.map(d => d.date),
            datasets: [{
                label: 'Pendapatan',
                data: trendData.map(d => d.revenue),
                borderColor: '#FF6B00',
                backgroundColor: 'rgba(255, 107, 0, 0.1)',
                fill: true,
                tension: 0.4,
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
                    ticks: {
                        callback: v => 'Rp ' + v.toLocaleString('id-ID')
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('revenueServiceChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(serviceData),
            datasets: [{
                data: Object.values(serviceData),
                backgroundColor: ['#FF6B00', '#2563EB', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    new Chart(document.getElementById('paymentMethodChart'), {
        type: 'pie',
        data: {
            labels: paymentData.map(d => d.payment_method),
            datasets: [{
                data: paymentData.map(d => d.total),
                backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.label + ': Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                    }
                }
            }
        }
    });
});
</script>
@endpush
