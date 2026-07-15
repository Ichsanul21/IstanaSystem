export const sidebarStore = {
    collapsed: false,
    mobileOpen: false,
    openSubmenus: [],
    init() {
        const saved = localStorage.getItem('sidebar-collapsed');
        if (saved !== null) {
            this.collapsed = saved === 'true';
        }
        const submenus = localStorage.getItem('sidebar-submenus');
        if (submenus !== null) {
            try { this.openSubmenus = JSON.parse(submenus); } catch {}
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
    toggleSubmenu(name) {
        if (this.openSubmenus.includes(name)) {
            this.openSubmenus = this.openSubmenus.filter(n => n !== name);
        } else {
            this.openSubmenus = [...this.openSubmenus, name];
        }
        localStorage.setItem('sidebar-submenus', JSON.stringify(this.openSubmenus));
    },
    isSubmenuOpen(name) {
        return this.openSubmenus.includes(name);
    },
};
