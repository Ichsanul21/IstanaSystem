# Istana Laundry System

> Sistem Manajemen Laundry Premium — Multi-Branch, Multi-Workshop, POS, CRM, Finance, Inventory.

**Tech Stack:** Laravel 13 • Blade • Alpine.js • Tailwind CSS v4 • MySQL/PostgreSQL • Chart.js • Midtrans

## Navigation

| Section | Description |
|---------|-------------|
| [Architecture](01-ARCHITECTURE.md) | Tech stack, folder structure, data flow |
| [Database Schema](02-DATABASE-SCHEMA.md) | All 28+ tables, relationships, indexes |
| [Role & Permission](03-ROLE-PERMISSION-MATRIX.md) | 8 roles, hierarchy, permission grid |
| [Design System](DESIGN/00-index.md) | Colors, typography, UI kit, layout |
| [Modules](MODULES/) | 14 business modules |
| [Features](FEATURES/) | Cross-cutting features (WA templates, public track page) |
| [API Contracts](API/00-overview.md) | All endpoint routes, request/response |
| [Testing](QUALITY/testing.md) | Test strategy, structure, commands |
| [Deployment](QUALITY/deployment.md) | Deploy steps, environment config |
| [Implementation Plan](implementation-plan.md) | Phase-by-phase execution |

## Quick Links

| Module | Doc |
|--------|-----|
| Auth & Users | [MODULES/01-auth-users.md](MODULES/01-auth-users.md) |
| Branch Management | [MODULES/02-branches.md](MODULES/02-branches.md) |
| Master Data | [MODULES/03-master-data.md](MODULES/03-master-data.md) |
| POS & Orders | [MODULES/04-pos-orders.md](MODULES/04-pos-orders.md) |
| Workshop & Production | [MODULES/05-workshop-production.md](MODULES/05-workshop-production.md) |
| CRM | [MODULES/06-crm.md](MODULES/06-crm.md) |
| Promotions | [MODULES/07-promotions.md](MODULES/07-promotions.md) |
| Finance | [MODULES/08-finance.md](MODULES/08-finance.md) |
| Inventory | [MODULES/09-inventory.md](MODULES/09-inventory.md) |
| Dashboard & Reports | [MODULES/10-dashboard-reports.md](MODULES/10-dashboard-reports.md) |
| Settings | [MODULES/11-settings.md](MODULES/11-settings.md) |
| Payment Gateway | [MODULES/12-payment-gateway.md](MODULES/12-payment-gateway.md) |
| Audit & Export | [MODULES/13-audit-export-backup.md](MODULES/13-audit-export-backup.md) |
| Customer Tracking | [MODULES/14-customer-tracking.md](MODULES/14-customer-tracking.md) |

## Brand Identity

- **Primary Color:** `#FF6B00` (orange)
- **Dark:** `#000000` (pure black)
- **Border:** `#E5E5E5` (light gray)
- **Font:** Inter (300–900 weight)
- **Style:** Royal/industrial hybrid — bold typography, monospace accents, barcode aesthetic

## Key Features

- **Multi-Branch:** Centralized management with branch-specific pricing, promotions, and inventory
- **Multi-Workshop:** Each branch belongs to a workshop; production tracking via QR code
- **8 User Roles:** Developer → Owner → Super Admin → Branch Admin → Workshop Admin → CS → Cashier → Workshop Staff
- **FIFO Inventory:** Full batch tracking with auto-journal COGS
- **Double-Entry Finance:** Auto-posting for POS, expenses, discounts, and tax
- **Tax Support:** PP 23/2018 (0.5%) or PKP (PPN 11%), auto-calculate and auto-journal
- **Midtrans Payment Gateway:** VA, QRIS, E-Wallet, Credit Card
- **Public Tracking:** Customer tracking page at `/track/{token}` with PIN verification
- **WhatsApp Integration:** wa.me links with pre-filled templates for all notifications
