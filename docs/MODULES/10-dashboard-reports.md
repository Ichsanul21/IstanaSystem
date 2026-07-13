# Module 10: Dashboard & Reports

## Overview

Role-specific dashboards with 5 tabs: Pendapatan, Operasional, Produksi, Keuangan, Inventory. Strategic view for Owner, operational view for Branch Admin.

## Dashboard Structure

```
DASHBOARD
├── 5 Tabs: [Pendapatan] [Operasional] [Produksi] [Keuangan] [Inventory]
│
├── Date Range Selector (Today / This Week / This Month / Custom)
│
├── Branch Selector (for Global roles)
│
└── Role-Based Content
```

## Tab: Pendapatan

| Widget | Type | Roles |
|--------|------|-------|
| Revenue Today (metric) | Number | All |
| Revenue Trend (7 days) | Line chart | All |
| Revenue by Service (pie) | Doughnut chart | Owner, SA, BA |
| Revenue by Branch (bar) | Bar chart | Owner, SA, Dev |
| Payment Method Breakdown | Pie chart | BA, SA |

## Tab: Operasional

| Widget | Type | Roles |
|--------|------|-------|
| Orders Today (metric) | Number | All |
| Average Order Value | Number | All |
| Orders Status Distribution | Doughnut | All |
| Top Customers (by orders) | Table | CS, BA, SA |
| Peak Hours (heatmap-like) | Bar chart | BA |
| Daily Capacity Usage | Progress | BA |

## Tab: Produksi

| Widget | Type | Roles |
|--------|------|-------|
| Items in Production | Number | WA, WS, BA |
| Queue per Status | Bar chart | WA, WS |
| Average Processing Time | Number | WA, BA |
| Workshop Performance | Metric | WA, BA |
| Items by Workshop (bar) | Bar chart | Owner, SA |

## Tab: Keuangan

| Widget | Type | Roles |
|--------|------|-------|
| Revenue vs Expense (period) | Comparison | Owner, SA, BA |
| Profit Margin (%) | Metric | Owner, SA |
| Monthly Trend | Area chart | Owner, SA |
| Top Expenses | Table | BA, SA |
| Tax Summary | Table | SA, Dev |
| Cash Flow Daily | Table | BA |

## Tab: Inventory

| Widget | Type | Roles |
|--------|------|-------|
| Stock Value | Metric | BA, SA |
| Low Stock Alerts | Alert list | BA, WA |
| Stock Movement (in/out) | Bar chart | BA |
| Inventory by Category | Pie chart | BA, SA |

## Role-Specific Default Views

| Role | Default Dashboard Tab | Extra |
|------|----------------------|-------|
| Developer | Keuangan | System Info widget |
| Owner | Pendapatan + Keuangan | Strategic summary, all branches |
| Super Admin | Operasional | Branch performance comparison |
| Branch Admin | Pendapatan + Operasional | Branch-specific metrics |
| Workshop Admin | Produksi | Workshop queue, status overview |
| CS | CRM | Customer stats |
| Cashier | Operasional | Today's orders |
| Workshop Staff | Produksi | My queue |

## UI

```
DASHBOARD
┌──────────────────────────────────────────────┐
│  [Today ▼]          [Cabang A ▼]   [08 Jul] │
├──────────────────────────────────────────────┤
│  [Pendapatan] [Operasional] [Produksi]       │
│  [Keuangan] [Inventory]                       │
├──────────────────────────────────────────────┤
│  ┌────────┬────────┬────────┬────────┐       │
│  │Revenue │ Orders │  Avg   │  In    │       │
|  │1.24 Jt │   12   │ 103rb  │  Prod  │       │
│  └────────┴────────┴────────┴────────┘       │
│                                               │
│  ┌────────────────────┐ ┌────────────────┐   │
│  │ Revenue Trend      │ │ Order Status   │   │
│  │ (line chart)       │ │ (doughnut)     │   │
│  └────────────────────┘ └────────────────┘   │
│                                               │
│  ┌────────────────────────────────────────┐   │
│  │ Recent Orders                          │   │
│  ├────┬──────────┬────────┬────────┬──────┤   │
│  │ #  │ Customer │ Total  │ Status │ Aksi │   │
│  ├────┼──────────┼────────┼────────┼──────┤   │
│  │001 │ Amir     │ 44.400 │ Cuci   │[Lihat]│  │
│  └────┴──────────┴────────┴────────┴──────┘   │
└──────────────────────────────────────────────┘
```

## Reports

| Report | Description | Format |
|--------|-------------|--------|
| Revenue | Pendapatan per periode | Excel, PDF |
| Orders | Daftar order dengan filter | Excel, PDF |
| Customers | Data pelanggan | Excel |
| Inventory | Stok per cabang | Excel |
| Tax | Ringkasan pajak | Excel, PDF |
| Production | Riwayat status per item | Excel, PDF |

## Files

```
app/Http/Controllers/Web/DashboardController.php
app/Http/Controllers/Web/ReportController.php
app/Services/Dashboard/DashboardService.php
app/Services/Dashboard/RevenueService.php
app/Services/Dashboard/ProductionService.php
app/Services/Dashboard/FinanceService.php
app/Services/Dashboard/InventoryService.php
resources/views/dashboard.blade.php
resources/views/dashboard/tabs/pendapatan.blade.php
resources/views/dashboard/tabs/operasional.blade.php
resources/views/dashboard/tabs/produksi.blade.php
resources/views/dashboard/tabs/keuangan.blade.php
resources/views/dashboard/tabs/inventory.blade.php
resources/views/dashboard/partials/metric-card.blade.php
resources/views/dashboard/partials/chart-card.blade.php
resources/views/dashboard/partials/recent-orders.blade.php
resources/views/dashboard/partials/low-stock-alert.blade.php
resources/views/reports/revenue.blade.php
resources/views/reports/orders.blade.php
resources/views/reports/customers.blade.php
resources/views/reports/inventory.blade.php
resources/views/reports/tax.blade.php
resources/views/reports/production.blade.php
```
