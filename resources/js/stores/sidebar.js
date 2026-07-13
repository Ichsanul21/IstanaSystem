export const sidebarStore = {
    collapsed: false,
    mobileOpen: false,
    init() {
        const saved = localStorage.getItem('sidebar-collapsed');
        if (saved !== null) {
            this.collapsed = saved === 'true';
        }
    },
    toggle() {
        this.collapsed = !this.collapsed;
        localStorage.setItem('sidebar-collapsed', this.collapsed);
    },
    toggleMobile() {
        this.mobileOpen = !this.mobileOpen;
    },
    closeMobile() {
        this.mobileOpen = false;
    },
};
