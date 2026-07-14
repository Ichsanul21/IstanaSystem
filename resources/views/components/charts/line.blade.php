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
                this.chart = new Chart(this.$el, {
                    type: 'line',
                    data: {
                        labels: @js($labels),
                        datasets: @js($datasets),
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
                    },
                });
            },
            destroy() {
                if (this.chart) this.chart.destroy();
            },
        }"
    ></canvas>
</div>
