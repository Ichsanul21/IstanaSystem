import Alpine from 'alpinejs';
import { themeStore } from './stores/theme';
import { sidebarStore } from './stores/sidebar';
import posCart from './pos-cart';

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
