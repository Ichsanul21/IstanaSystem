# Layout

## Structure (TailAdmin Pro Pattern)

```
┌─────────────────────────────────────────────────────────┐
│  STATUS BAR (fixed top-0, h-9, black bg, orange dot)    │
│  "Supported by Alenkosa" | "ISTANA LAUNDRY" | HH:MM:SS  │
├───────────┬─────────────────────────────────────────────┤
│           │                                             │
│ SIDEBAR   │  HEADER (sticky, white bg, border-b)        │
│           │  [☰] [Logo] [Search K/Cmd] [🔔] [👤] [🌙] │
│ dark bg   ├─────────────────────────────────────────────┤
│ 290px ex  │                                             │
│  90px col │  CONTENT AREA                               │
│           │  max-w-(--breakpoint-2xl) mx-auto           │
│           │  p-4 md:p-6                                 │
│           │                                             │
│           │  ┌────────────────────────────────────────┐ │
│           │  │ 12-col grid (tailwind)                 │ │
│           │  └────────────────────────────────────────┘ │
│           │                                             │
└───────────┴─────────────────────────────────────────────┘
```

## Sidebar (AppSidebar)

| State | Width | Behavior |
|-------|-------|----------|
| **Expanded** | `xl:ml-[290px]` | Full sidebar with labels |
| **Collapsed** | `xl:ml-[90px]` | Icons only, labels shown on hover |
| **Mobile** | Fixed overlay | Slide-in from left, backdrop dims content |

### Sidebar Sections

```
SIDEBAR
├── LOGO (full when expanded, icon-only when collapsed)
│
├── MAIN MENU
│   ├── Dashboard
│   ├── POS
│   ├── Orders
│   ├── Workshop / Produksi ─┬─ Queue
│   │                         ├─ Scan QR
│   │                         └─ Stats
│   ├── Customers / CRM ──────┬─ Daftar Pelanggan
│   │                          ├─ Membership Tiers
│   │                          └─ Loyalty Points
│   ├── Promotions
│   ├── Inventory ────────────┬─ Items
│   │                          ├─ Stock
│   │                          └─ Batches
│   ├── Finance ──────────────┬─ Dashboard
│   │                          ├─ Journal
│   │                          ├─ Chart of Accounts
│   │                          ├─ Pajak
│   │                          └─ Pengeluaran
│   ├── Reports ──────────────┬─ Pendapatan
│   │                          └─ Orders
│   ├── Settings
│   ├── Users
│   └── Activity Logs
│
└── SIDEBAR WIDGET (promo / version info)
```

### Sidebar Behavior (Alpine.js Store)

```javascript
// Sidebar Store — replicated from TailAdmin's SidebarContext
Alpine.store('sidebar', {
    collapsed: false,
    mobileOpen: false,
    activeItem: null,
    openSubmenus: [],

    toggle() { this.collapsed = !this.collapsed },
    toggleMobile() { this.mobileOpen = !this.mobileOpen },
    closeMobile() { this.mobileOpen = false },
    toggleSubmenu(name) {
        if (this.openSubmenus.includes(name)) {
            this.openSubmenus = this.openSubmenus.filter(n => n !== name);
        } else {
            this.openSubmenus = [...this.openSubmenus, name];
        }
    },
    isActive(path) { /* highlight current route */ },
})
```

## Header (AppHeader)

| Element | Description |
|---------|-------------|
| **Hamburger** | Toggle sidebar (desktop) / mobile sidebar (mobile) |
| **Logo** | Mobile-only logo display |
| **Search** | `Ctrl+K` / `Cmd+K` shortcut, visible on `xl:` |
| **Notifications** | Bell icon dropdown — order updates, low stock alerts |
| **User Menu** | Avatar + name dropdown — profile, settings, logout |
| **Theme Toggle** | Sun/moon icon — light/dark switch |
| **Branch Selector** | `<select>` dropdown — switches active branch via `admin.branch.switch` |

## Content Area

| Breakpoint | Padding | Max Width |
|-----------|---------|-----------|
| Default | `p-4` | — |
| `md:` | `md:p-6` | — |
| `xl:` | — | `max-w-(--breakpoint-2xl)` |

### Grid System (12-column)

```blade
<div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12 xl:col-span-7">
        <!-- Main content -->
    </div>
    <div class="col-span-12 xl:col-span-5">
        <!-- Side content -->
    </div>
    <div class="col-span-12">
        <!-- Full width -->
    </div>
</div>
```

## Route-Specific Styles (from TailAdmin)

- Default pages: `p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6`
- Full-width pages (auth, errors): No padding restrictions
- Blank page: Minimal layout
