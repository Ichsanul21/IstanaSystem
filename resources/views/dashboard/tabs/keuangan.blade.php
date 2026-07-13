@props(['revenueVsExpense' => [], 'profitMargin' => 0, 'monthlyTrend' => []])

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
    <x-dashboard.partials.metric-card
        label="Margin Keuntungan"
        value="{{ number_format($profitMargin, 1, ',', '.') }}%"
        icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>'
        :trend="profitMargin >= 0 ? 'Profitabel' : 'Rugi'"
        :trend-up="profitMargin >= 0"
    />
    <x-dashboard.partials.metric-card
        label="Pendapatan"
        value="Rp {{ number_format($revenueVsExpense['revenue'] ?? 0, 0, ',', '.') }}"
        icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    />
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <x-dashboard.partials.chart-card title="Pendapatan vs Pengeluaran" id="revExpChart" />
    <x-dashboard.partials.chart-card title="Tren Bulanan (6 Bulan)" id="monthlyTrendChart" />
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const revExp = @json($revenueVsExpense);

    new Chart(document.getElementById('revExpChart'), {
        type: 'bar',
        data: {
            labels: ['Pendapatan', 'Pengeluaran'],
            datasets: [{
                data: [revExp.revenue || 0, revExp.expense || 0],
                backgroundColor: ['#10B981', '#EF4444'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.label + ': Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                    }
                }
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

    const monthlyData = @json($monthlyTrend);

    new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'line',
        data: {
            labels: monthlyData.map(d => d.month),
            datasets: [
                {
                    label: 'Pendapatan',
                    data: monthlyData.map(d => d.revenue),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: 'Pengeluaran',
                    data: monthlyData.map(d => d.expense),
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
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
});
</script>
@endpush
