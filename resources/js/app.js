import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Chart from 'chart.js/auto';
import { themeStore } from './stores/theme';
import { sidebarStore } from './stores/sidebar';
import posCart from './pos-cart';

Alpine.plugin(collapse);

window.Chart = Chart;

Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.color = '#666666';
Chart.defaults.borderColor = '#E5E5E5';

window.Alpine = Alpine;

Alpine.store('theme', themeStore);
Alpine.store('sidebar', sidebarStore);

Alpine.data('posCart', posCart);

Alpine.data('notification', () => ({
    visible: false,
    message: '',
    type: 'success',
    init() {
        if (this.$el.dataset.message) {
            this.message = this.$el.dataset.message;
            this.type = this.$el.dataset.type || 'success';
            this.visible = true;
            setTimeout(() => this.visible = false, 5000);
        }
    }
}));

Alpine.start();
