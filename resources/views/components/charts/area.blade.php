@props([
    'labels' => [],
    'datasets' => [],
    'height' => 'h-80',
])

<div class="{{ $height }} relative">
    <canvas
        x-data="{
            chart: null,
            init() {
                const datasets = @js($datasets).map(ds => ({
                    ...ds,
                    fill: true,
                    backgroundColor: ds.backgroundColor || 'rgba(255, 107, 0, 0.1)',
                }));
                this.chart = new Chart(this.$el, {
                    type: 'line',
                    data: {
                        labels: @js($labels),
                        datasets: datasets,
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                        },
                        scales: {
                            y: { beginAtZero: true },
                        },
                        elements: {
                            line: { tension: 0.3 },
                        },
                    },
                });
            },
            destroy() {
                if (this.chart) this.chart.destroy();
            },
        }"
    ></canvas>
</div>
