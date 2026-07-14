@props([
    'labels' => [],
    'data' => [],
    'colors' => ['#FF6B00', '#000000', '#E5E5E5', '#10B981'],
    'height' => 'h-72',
])

<div class="{{ $height }} relative">
    <canvas
        x-data="{
            chart: null,
            init() {
                this.chart = new Chart(this.$el, {
                    type: 'doughnut',
                    data: {
                        labels: @js($labels),
                        datasets: [{
                            data: @js($data),
                            backgroundColor: @js($colors),
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
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
