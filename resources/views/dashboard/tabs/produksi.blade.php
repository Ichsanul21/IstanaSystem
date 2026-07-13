@props(['queuePerStatus' => [], 'itemsInProduction' => 0, 'avgProcessingTime' => 0])

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
    <x-dashboard.partials.metric-card
        label="Dalam Produksi"
        value="{{ number_format($itemsInProduction, 0, ',', '.') }}"
        icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-8.69 3.56a1 1 0 00-.74.02l.98-4.27s.39-1.35-.23-2.12C2.08 11.2 1 9.68 1 9.68l5.1-3.11s2.07 1.1 3.34 1.22c1.28.13 2.53-.64 2.53-.64l6.12 2.7s.53 2.33.09 3.69c-.44 1.36-1.53 2.39-1.53 2.39l-4.7-1.97s-.78.33-1.53.11z"/></svg>'
    />
    <x-dashboard.partials.metric-card
        label="Rata-rata Waktu Produksi"
        value="{{ $avgProcessingTime > 60 ? number_format($avgProcessingTime / 60, 1, ',', '.') . ' Jam' : number_format($avgProcessingTime, 0, ',', '.') . ' Menit' }}"
        icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    />
    <x-dashboard.partials.metric-card
        label="Siap Ambil"
        value="{{ $queuePerStatus['ready_for_pickup'] ?? 0 }}"
        icon='<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    />
</div>

<x-dashboard.partials.chart-card title="Antrian per Status Produksi" id="productionQueueChart" />

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const queueData = @json($queuePerStatus);
    const labels = {
        received: 'Diterima',
        washed: 'Dicuci',
        dried: 'Dikeringkan',
        ironed: 'Disetrika',
        packed: 'Dikemas',
        ready_for_pickup: 'Siap Ambil',
    };

    new Chart(document.getElementById('productionQueueChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(queueData).map(k => labels[k] || k),
            datasets: [{
                label: 'Jumlah Item',
                data: Object.values(queueData),
                backgroundColor: ['#6B7280', '#3B82F6', '#F59E0B', '#8B5CF6', '#10B981', '#FF6B00'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});
</script>
@endpush
