# Istana Laundry System

Sistem manajemen laundry **multi-cabang multi-workshop** dengan fitur POS, QR-based production tracking, CRM (poin & membership), double-entry finance, FIFO inventory, dan Midtrans payment gateway.

---

## Tech Stack

| Lapisan | Teknologi |
|---------|-----------|
| Framework | Laravel 13 + Blade |
| CSS | Tailwind CSS v4 (`@tailwindcss/vite`, no `tailwind.config.js`) |
| JS | Alpine.js 3 (stores: theme, sidebar) + Chart.js (CDN) |
| Auth | Laravel Breeze (Blade) v2.4 |
| Roles | Spatie Permission v8 (8 roles, 37 permissions) |
| Payments | Midtrans Snap v2.6 (VA, QRIS, E-Wallet, Kartu) |
| PDF | DomPDF dev-master |
| Excel | Laravel Excel 3.1 |
| Backup | Spatie Backup 10.3 |
| Database | SQLite (dev), MySQL / PostgreSQL (prod) |

---

## Fitur Utama

- **Multi-branch** dengan branch switching dan scoping otomatis
- **Multi-workshop** untuk sentralisasi produksi
- **POS** dengan cart, promo, points, tax
- **Produksi** tracking via QR code (8 status, forward-only)
- **CRM** — membership 4 tier (Bronze/Silver/Gold/Platinum), loyalty points
- **Promosi** — percentage, fixed, buy X get Y
- **Finance** — double-entry, COA, jurnal, trial balance, P&L, balance sheet
- **Inventory** — FIFO dengan batch tracking
- **Midtrans** gateway (Snap popup + webhook)
- **Public tracking** — `/track/{token}` dengan PIN verification
- **Backup & audit log**
- **8 role pengguna** — Developer, Super Admin, Owner, Branch Admin, Workshop Admin, CS, Cashier, Workshop Staff
- **Dark mode** — class-based, localStorage persistence
- **14 modul export** (Excel/PDF)

---

## Arsitektur

### Data Flow

```
User → Blade → HTTP → Controller → Service Layer → Eloquent → DB
                                                          ↓
                                                     Response
```

### Route Groups

| Group | Middleware | Prefix | Fungsi |
|-------|-----------|--------|--------|
| Public | — | `GET /track/{token}` | Tracking pesanan (tanpa auth) |
| Admin | `auth`, `verified`, `branch` | `admin.*` | Semua fitur internal |
| API v1 | `auth:sanctum` | `/api/v1/*` | REST API (orders, customers, dll) |
| Webhook | `api`, CSRF excluded | `/api/webhook/midtrans` | Midtrans callback |

### Branch Scoping

1. `SetBranchContext` middleware menyimpan `current_branch_id` di session
2. Helper `currentBranchId()` mengembalikan branch aktif (`null` jika tidak ada)
3. Trait `HasBranchScope` — `scopeForCurrentBranch()` otomatis memfilter query
4. Model dengan branch scope: `Order`, `Customer`, `ServicePricing`, `JournalEntry`, `ActivityLog`, `DailyCashFlow`

### Middleware

| Alias | Class | Fungsi |
|-------|-------|--------|
| `branch` | `SetBranchContext` | Set branch context ke session |
| `role` | `CheckRole` | Cek role (comma-separated, e.g. `role:Developer,Super Admin`) |
| `webhook.signature` | `VerifyWebhookSignature` | Registered, tidak dipakai di routes |

---

## 14 Modul

| # | Modul | Folder Kunci |
|---|-------|--------------|
| 1 | Auth & Users | `routes/auth.php`, `Controllers/Auth/`, `Models/User.php` |
| 2 | Branches | `Controllers/Web/BranchController.php`, `Models/Branch.php` |
| 3 | Master Data (Services, Pricing) | `Controllers/Web/ServiceController.php`, `Models/Service.php` |
| 4 | POS & Orders | `Controllers/Web/POSController.php`, `OrderController.php` |
| 5 | Workshop & Production | `Controllers/Web/WorkshopController.php`, `ScannerController.php` |
| 6 | CRM (Customers, Membership, Points) | `Controllers/Web/CustomerController.php` |
| 7 | Promotions | `Controllers/Web/PromotionController.php`, `Services/PromotionService.php` |
| 8 | Finance (Double-Entry, COA, Tax) | `Controllers/Web/FinanceController.php`, `Services/FinanceService.php` |
| 9 | Inventory (FIFO, Batch) | `Controllers/Web/InventoryItemController.php` |
| 10 | Dashboard & Reports | `Controllers/Web/DashboardController.php`, `ReportController.php` |
| 11 | Settings | `Controllers/Web/SettingsController.php`, `Services/SettingService.php` |
| 12 | Payment Gateway (Midtrans) | `Controllers/Api/V1/PaymentWebhookController.php` |
| 13 | Audit, Export, Backup | `Controllers/Web/AuditController.php`, `ExportController.php`, `BackupController.php` |
| 14 | Customer Tracking (Public) | `Controllers/Web/TrackingController.php` |

---

## Struktur Direktori

```
app/
  Enums/
    OrderStatus.php           # draft, pending, processing, completed, cancelled
    ProductionStatus.php      # TERIMA → PILAH → CUCI → KERING → LIPAT → CEK → SIAP → DIAMBIL
    PaymentMethod.php         # cash, transfer, qris, gateway
    PromotionType.php         # percentage, fixed, buy_x_get_y
    TaxRegime.php             # none, pp23, pkp
  Models/                     # 33 model (tanpa HasFactory)
    Order.php
    OrderItem.php
    OrderItemStatusLog.php
    ProductionStatus.php
    Customer.php
    ...
  Services/
    SettingService.php        # Global & branch settings
    PromotionService.php      # Eligibility, usage tracking
    FinanceService.php        # Double-entry journal, revenue/expense
    DiscountCalculator.php    # Percentage, fixed, buy-get
  Traits/
    HasBranchScope.php        # scopeForBranch, scopeForCurrentBranch
    LogsActivity.php          # Auto-log CRUD events
    HasQrToken.php            # Generate QR token
    GeneratesOrderNumber.php  # Auto-generate {BranchCode}-{YYYYMMDD}-{XXXXX}
  Http/
    Controllers/
      Web/                    # 30 controllers (Blade)
      Api/V1/                 # 14 controllers (REST API)
      Auth/                   # 8 controllers (Breeze)
    Middleware/
      SetBranchContext.php    # alias: branch
      CheckRole.php           # alias: role
      VerifyWebhookSignature.php

resources/
  views/
    dashboard.blade.php       # Root level (bukan dashboard/index)
    layouts/admin.blade.php   # Admin layout (x-layouts.admin component)
    components/ui/            # button, card, badge, table, modal, alert, dll
    {module}/{action}.blade.php
  css/app.css                 # Tailwind v4 + @theme design tokens
  js/
    app.js                    # Entry: Alpine stores + pos-cart
    stores/theme.js           # Dark mode toggle (localStorage)
    stores/sidebar.js         # Sidebar collapse (localStorage)
    pos-cart.js               # POS cart management

routes/
  web.php                     # Web routes (admin.*, public tracking)
  api.php                     # API v1 routes (Sanctum)
  auth.php                    # Breeze auth routes
  webhook.php                 # Midtrans webhook

database/
  migrations/                 # 68 file
  seeders/                    # 7 seeder (idempotent)

tests/                        # 185 test (14 unit + 21 feature)
docs/                         # 44 file dokumentasi lengkap
```

---

## Setup Lokal

### Requirements

- PHP ^8.3
- Composer
- Node.js + npm
- SQLite (atau MySQL / PostgreSQL)

### Instalasi

```bash
# 1. Clone & masuk direktori
git clone <repo-url>
cd IstanaLaundrySystem

# 2. Install dependencies
composer install
npm install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Database & seed
php artisan migrate:fresh --seed

# 5. Build assets (production)
npm run build
```

### Menjalankan Dev

```bash
# All-in-one (server + queue + log + vite)
composer run dev

# Atau manual (2 terminal)
npm run dev           # Terminal 1: Vite hot reload
php artisan serve     # Terminal 2: Laravel di http://localhost:8000
```

---

## Testing

```bash
# Full suite (config:clear + php artisan test)
composer run test

# Subset
php artisan test --filter WorkshopTest
php artisan test --filter OrderStatusTest
php artisan test Tests/Feature/Web/OrderControllerTest

# Stop on first failure
php artisan test --stop-on-failure
```

**Setup**: 185 test (14 unit + 21 feature), SQLite in-memory, data di-seed otomatis per-test via `TestCase::setUp()`.

---

## Perintah Berguna

| Perintah | Fungsi |
|----------|--------|
| `composer run dev` | Dev all-in-one (server + queue + log + vite) |
| `composer run test` | Menjalankan test |
| `npm run dev` | Vite HMR (hot reload) |
| `npm run build` | Build asset production |
| `php artisan serve` | Laravel dev server |
| `php artisan migrate:fresh --seed` | Reset database + seed |
| `php artisan route:list --except-vendor` | Lihat semua route |
| `php artisan optimize:clear` | Clear cache |

---

## Konvensi Developer

### Route Names

Semua admin route pakai prefix `admin.*`:

```php
// ✅ Benar
route('admin.orders.index')
route('admin.dashboard')

// ❌ Salah — RouteNotFoundException
route('orders.index')
```

### Blade Component

```blade
<x-layouts.admin>
    <x-slot:header>Judul Halaman</x-slot:header>

    <x-ui.card>
        <x-ui.table :headers="[...]" :rows="[...]" />
    </x-ui.card>

    <x-ui.button variant="primary">Simpan</x-ui.button>
    <x-ui.button variant="danger" :disabled="$isProtected">Hapus</x-ui.button>

    <x-ui.badge variant="success">Selesai</x-ui.badge>
</x-layouts.admin>
```

Available components: `button`, `card`, `badge`, `table`, `modal`, `alert`, `tabs`, `pagination`, `input`, `select`, `label`, `textarea`, `progress`, `dropdown`.

### Enums — Order Status

```php
App\Enums\OrderStatus

// Values: draft, pending, processing, completed, cancelled
// Methods: label() → Indonesia, color() → gray/warning/info/success/danger, sequence() → 0-4
// Helper: OrderStatus::from('pending')->label() → 'Baru'
```

### Enums — Production Status

```php
App\Enums\ProductionStatus

// Values: TERIMA, PILAH, CUCI, KERING, LIPAT, CEK, SIAP, DIAMBIL
// Methods: sequence(), label() → Indonesia, color(), next(), isTerminal()
// Rule: forward-only, no skipping, no rollback
```

### CSS Design Tokens

Tailwind CSS v4 — `@theme` di `resources/css/app.css`:

| Token | Value | Penggunaan |
|-------|-------|------------|
| `primary` | `#FF6B00` | Brand orange |
| `primary-dark` | `#E55F00` | Hover state |
| `primary-light` | `#FF8533` | Light variant |
| `dark` | `#000000` | Hitam |
| `lo-gray` | `#E5E5E5` | Abu-abu terang |
| `success` | `#10B981` | Hijau |
| `warning` | `#F59E0B` | Amber |
| `error` | `#EF4444` | Merah |
| `info` | `#3B82F6` | Biru |

Gunakan di Blade: `class="bg-primary"`, `class="text-success"`.

### Larangan

- **Jangan** gunakan `@php use App\Enums\*;` di Blade — pakai FQCN atau precompute di controller
- **Jangan** comment di Blade template
- **Jangan** tambah `HasFactory` trait — Laravel 13 tidak require

### Seeder

Semua seeder harus **idempotent** (gunakan `firstOrCreate` / `updateOrCreate`):

```php
ProductionStatus::firstOrCreate(
    ['code' => 'TERIMA'],
    ['name' => 'Terima', 'sequence' => 1]
);
```

Urutan seeding: `RolePermissionSeeder` → `MembershipTierSeeder` → `DefaultSettingsSeeder` → `ServiceSeeder` → `ProductionStatusSeeder` → `ChartOfAccountSeeder` → `SampleDataSeeder`.

---

## Environment Variables

File `.env.example` sudah mencakup semua variabel. Yang perlu disesuaikan:

| Variabel | Dev | Prod |
|----------|-----|------|
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `DB_CONNECTION` | `sqlite` | `mysql` / `pgsql` |
| `SESSION_DRIVER` | `database` | `database` |
| `QUEUE_CONNECTION` | `database` | `database` |
| `CACHE_STORE` | `database` | `redis` |

> **Catatan**: Midtrans key (`MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`) tidak ada di `.env.example` — tambahkan manual jika perlu gateway production.

---

## Dokumentasi Lengkap

Seluruh dokumentasi detail ada di folder `docs/`:

| Path | Isi |
|------|-----|
| `docs/00-README.md` | Index dokumentasi |
| `docs/01-ARCHITECTURE.md` | Arsitektur & tech stack |
| `docs/02-DATABASE-SCHEMA.md` | 35 tabel, ERD, indexes |
| `docs/03-ROLE-PERMISSION-MATRIX.md` | 8 roles, 37 permissions |
| `docs/MODULES/` | 14 module specs (flow, UI wireframe, file list) |
| `docs/DESIGN/` | Design system (tokens, layout, components, forms, tables, charts) |
| `docs/API/` | REST API docs (14 files, request/response JSON) |
| `docs/FEATURES/` | Fitur spesifik (WA templates, tracking page) |
| `docs/QUALITY/` | Testing strategy & deployment guide |
| `docs/implementation-plan.md` | Rencana implementasi 19 fase |

---

## Lisensi

MIT
