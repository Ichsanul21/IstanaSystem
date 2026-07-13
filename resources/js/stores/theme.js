export const themeStore = {
    dark: false,
    init() {
        const saved = localStorage.getItem('theme');
        if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            this.dark = true;
        }
        this.apply();
    },
    toggle() {
        this.dark = !this.dark;
        this.apply();
        localStorage.setItem('theme', this.dark ? 'dark' : 'light');
    },
    apply() {
        if (this.dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },
};
