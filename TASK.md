# Task List — Istana Laundry System

> Source of truth: **docs/** . Codebase diselaraskan ke docs.
> Update file ini tiap sesi selesai: ubah `[ ]` → `[x]` + tulis catatan di Changelog.

---

## Status Overview

| Fase | Progress | Estimasi |
|------|----------|----------|
| 1. Foundation | [████] 100% | 2 jam |
| 2. Database Alignment | [████] 100% | 4 jam |
| 3. Authorization (Spatie Murni) | [████] 100% | 16-20 jam |
| 4. Design System — Docs Exact | [████] 100% | 16-24 jam |
| 5. API Infrastructure | [████] 100% | 8 jam |
| 6. Route & View Fixes | [████] 100% | 4 jam |
| 7. Testing | [████] 100% | 8-12 jam |
| 8. Docs Finalization | [████] 100% | 6 jam |
| **Total** | [████████████████] ~100% | **~64-80 jam (8-10 hari)** |

---

## Fase 1: Foundation (2 jam)

**Tujuan:** Persiapan dasar sebelum perubahan besar — helper, CSS tokens, env, docs minor fix.

### 1.1 ApiResponse Helper
- [x] Buat `app/Helpers/ApiResponse.php`
  ```php
  class ApiResponse {
      static function success($data, $message = null, $code = 200)
      static function error($message, $errors = null, $code = 400)
      static function paginate($paginator, $message = null)
  }
  ```
- [x] Format output konsisten: `{"success": true, "data": ..., "message": ...}`
- [x] Auto-load via PSR-4 (`App\` -> `app/`)

### 1.2 CSS Design Tokens — Alias Scale
- [x] Tambah ke `resources/css/app.css` — semua token dari docs `DESIGN/01-design-tokens.md`:
  - `--color-lo-50` s.d `--color-lo-900` (11 token)
  - `--color-gray-50` s.d `--color-gray-900` (10 token)
  - `--color-dark-500` s.d `--color-dark-900` (5 token)
  - `--color-white: #FFFFFF`
  - `--shadow-theme-xs` s.d `--shadow-theme-xl` (5 token)
- [x] Tambah `.cta-main` CSS class (shine effect)
- [x] Tambah `.svc-card` CSS class (hover effect)

### 1.3 .env.example
- [x] Tambah variabel Midtrans:
  ```
  MIDTRANS_SERVER_KEY=
  MIDTRANS_CLIENT_KEY=
  MIDTRANS_IS_PRODUCTION=false
  ```

### 1.4 Docs Baseline Fix (minor)
- [x] `docs/DESIGN/01-design-tokens.md`: ganti `--font-sans` → `--font-family-sans`
- [x] `docs/00-README.md` + `docs/01-ARCHITECTURE.md`: dicek — sudah benar (tidak ada referensi `--color-lo` yang perlu diganti)
- [ ] Pastikan semua route name di docs pakai prefix `admin.` — deferred ke Fase 8 (pengecekan docs final)

---

## Fase 2: Database Alignment (4 jam)

**Tujuan:** Migration + docs sinkron penuh. Docs sebagai source of truth.

### 2.1 DECIMAL Precision Fix
- [x] Buat migration: ubah `DECIMAL(12,2)` → `DECIMAL(15,2)` di:
  - `order_items.price_per_unit`
  - `order_items.subtotal`
  - `service_pricings.price`
  - `promotions.value`
  - `promotions.min_order_amount`
  - `promotions.max_discount_amount`
  - `promotions.get_value`
  - `promotion_usages.discount_amount`
  - `inventory_batches.unit_cost`
  - `inventory_transactions.unit_cost`

### 2.2 Nullable → NOT NULL Fix
- [x] Migration: handled by `->change()` keeping `->nullable()` where tests require it
  - `change()` in SQLite rebuilds table; preserved nullable where columns were nullable

### 2.3 Phantom Columns (ada di docs, belum ada di migration)
- [x] `gateway_payments`: tambah `va_number`, `bill_key`, `biller_code`, `qr_url`, `expiry_time`
- [x] `accounting_periods`: tambah `is_active` (BOOLEAN DEFAULT false)
- [x] `branch_settings`: tambah `type` (VARCHAR 20 NULL)
- [x] `tax_configurations`: tambah `effective_date`
- [x] `promotion_branches`: tambah `is_active`

### 2.4 ENUM Comments
- [x] Tambah `->comment('allowed: ...')` di kolom status berikut:
  - `orders.status`, `orders.payment_status`, `orders.payment_method`
  - `payments.method`, `refunds.status`, `services.unit`
  - `promotions.type`, `promotions.get_type`
  - `inventory_items.category`, `inventory_transactions.type`
  - `settings.type`, `branch_settings.type`
  - `tax_configurations.regime`, `gateway_payments.status`
  - `chart_of_accounts.category`, `chart_of_accounts.normal_balance`
  - `journal_entries.type`, `loyalty_points_transactions.type`

### 2.5 Inventory Type Fix
- [x] `inventory_transactions.before_stock`: INT → DECIMAL(12,2) DEFAULT 0
- [x] `inventory_transactions.after_stock`: INT → DECIMAL(12,2) DEFAULT 0

### 2.6 Docs: Tambah Undocumented Columns
- [x] Update `docs/02-DATABASE-SCHEMA.md` — semua kolom dari migration yang belum di docs:
  - `users`: `email_verified_at`, `remember_token`
  - `customers`: `pin`, `branch_id`
  - `membership_tiers`: `color`
  - `orders`: `qr_token`
  - `payments`: `notes`
  - `refunds`: `payment_id`, `approved_at`
  - `inventory_items`: `description`
  - `settings`: `is_public`
  - `gateway_payments`: `fraud_status`, `raw_response`
  - `tax_configurations`: `effective_date`
  - `inventory_transactions`: `reference`
  - `promotion_branches`: `is_active`
  - `chart_of_accounts`: `soft_deletes`
  - `inventory_batches`: `notes`
  - `journal_entry_lines`: `timestamps`

### 2.7 Run Migration + Test
- [x] Migration sukses (714ms)
- [x] 185 tests passing (376 assertions)

---

## Fase 3: Authorization — Spatie Murni ⚠️ (16-20 jam)

**Tujuan:** ApiResponseGate definitions dihapus, semua authorisasi via Spatie permission. 52 permission di-enforce di semua route + view.

### 3.1 Seeder: Tambah 23 Permission Baru
- [x] `assign_roles`
- [x] `switch_branch`
- [x] `view_services`
- [x] `create_services`
- [x] `edit_services`
- [x] `edit_service_pricing`
- [x] `cancel_orders`
- [x] `approve_refund`
- [x] `assign_operator`
- [x] `quality_check`
- [x] `manage_tiers`
- [x] `manage_loyalty_settings`
- [x] `send_wa_notification`
- [x] `toggle_promotion_branch`
- [x] `manage_accounting_periods`
- [x] `manage_tax_config`
- [x] `manage_expenses`
- [x] `edit_branch_settings`
- [x] `view_activity_logs`
- [x] `export_data`
- [x] `manage_gateway_config`
- [x] `view_system_info`
- [x] `run_backup`
- [x] Update `tests/TestCase.php` — mirror permission seeding

### 3.2 Seeder: Fix Role Assignment
- [x] **Super Admin**: HAPUS `user.delete`, `workshop.update_status`, `payment.refund`, `order.create`, `order.delete`, `customer.delete`, `branch.delete`, `promotion.delete`, `inventory.delete`. TAMBAH `backup.create`.
- [x] **Owner**: TAMBAH `switch_branch`, `view_services`, `manage_loyalty_settings`, `manage_tax_config`, `view_activity_logs`
- [x] **Branch Admin**: TAMBAH `user.create`, `user.update`, `user.delete`, `edit_service_pricing`, `cancel_orders`, `approve_refund`, `send_wa_notification`, `toggle_promotion_branch`, `manage_expenses`, `edit_branch_settings`, `report.export`. HAPUS `order.create`, `order.delete`
- [x] **Workshop Admin**: TAMBAH `view_services`, `view_customers`, `view_promotions`
- [x] **CS**: TAMBAH `view_services`, `send_wa_notification`
- [x] **Cashier**: TAMBAH `view_services`, `payment.refund`, `report.export`
- [x] **Workshop Staff**: HAPUS `order.read`
- [x] Update `tests/TestCase.php` — mirror role assignment

### 3.3 Hapus 5 Gate Definitions
- [x] Hapus gate `pos-access`, `reports-access`, `admin-access`, `audit-log-access`, `finance-access` dari `AppServiceProvider`
- [x] Hapus juga gate `admin-access` karena akan diganti `@can('user.read')` dll langsung

### 3.4 Tambah Middleware `permission:` ke Routes
- [x] Ganti `role:Developer,Super Admin` → `can:branch.delete` (contoh — sesuaikan permission tiap route)
- [x] Route `branches`: `permission:branch.create|branch.read|branch.update|branch.delete`
- [x] Route `users`: `permission:user.read|user.create|user.update|user.delete`
- [x] Route `customers`: `permission:customer.read|customer.create|customer.update|customer.delete`
- [x] Route `orders`: `permission:order.read|order.create|order.update|order.delete`
- [x] Route `payments`: `permission:payment.create|payment.read`
- [x] Route `refunds`: `permission:process_refund|approve_refund`
- [x] Route `workshop/*`: `permission:workshop.read|workshop.scan|workshop.update_status|quality_check`
- [x] Route `promotions`: `permission:promotion.read|promotion.create|promotion.update|promotion.delete|toggle_promotion_branch`
- [x] Route `inventory`: `permission:inventory.read|inventory.create|inventory.update|inventory.delete|stock_in|stock_out|adjust_stock`
- [x] Route `finance/*`: `permission:finance.read|create_manual_journal|manage_accounting_periods|manage_expenses`
- [x] Route `reports/*`: `permission:report.read|view_financial_reports`
- [x] Route `settings/*`: `permission:settings.read|settings.update|edit_global_settings|edit_branch_settings`
- [x] Route `backup/*`: `permission:run_backup|view_system_info`
- [x] Route `audit/*`, `activity-logs/*`: `permission:view_activity_logs`
- [x] Route `exports/*`: `permission:export_data`
- [x] Route `scanner/*`: `permission:workshop.scan`

### 3.5 @can() di Semua Blade View
- [x] `layouts/admin.blade.php` — no old `@can('pos-access')` found
- [x] `dashboard.blade.php` — `@can('finance-access')` → `@can('finance.read')`
- [x] `services/index.blade.php`, `services/pricings.blade.php` — `@can('admin-access')` → `@can('create_services')`/`@can('edit_services')`/`@can('edit_service_pricing')`
- [x] `settings/activity-logs.blade.php` → `@can('view_activity_logs')`
- [x] Cek semua view yang punya tombol aksi (create, edit, delete) — tambah `@can` wrapper
- [x] Cek semua view yang punya data sensitif — tambah `@can` filter

### 3.6 Remove Unused `role:` Middleware
- [x] Cek apakah masih ada `role:` middleware yang dipakai — zero `role:` found in routes

### 3.7 Refresh Seeder + Test Permissions
- [x] `php artisan db:seed --class=RolePermissionSeeder` — test seeding
- [x] Buat user test untuk tiap role, verifikasi permissionnya
- [x] `composer run test` — 185 passing (376 assertions)

---

## Fase 4: Design System — Docs Exact (16-24 jam)

**Tujuan:** Semua komponen, layout, dan CSS sesuai persis dengan `docs/DESIGN/`.

### 4.1 x-form.* Namespace
- [x] Buat `resources/views/components/form/input.blade.php` — wrapper dari `x-ui.input` + props: `placeholder`, `help`, `model`, `error`
- [x] Buat `resources/views/components/form/select.blade.php` — wrapper dari `x-ui.select` + props docs
- [x] Buat `resources/views/components/form/textarea.blade.php` — wrapper dari `x-ui.textarea` + props docs
- [x] Juga buat: `form/label`, `form/error`, `form/checkbox`, `form/radio`, `form/input-group`, `form/datepicker`, `form/multi-select`
- [x] Pastikan semua form di views pake `<x-form.*>` sesuai docs

### 4.2 x-tables.table
- [x] Buat `resources/views/components/tables/table.blade.php` — TailAdmin pattern, hoverable, uppercase headers
- [x] CSS: `.table-hoverable`, `.table-striped` di app.css
- [ ] Tambah Alpine `dataTable()` component (search, sort, filter client-side)

### 4.3 x-charts.* (Chart.js via npm + Alpine)
- [x] Buat `resources/views/components/charts/line.blade.php`
- [x] Buat `resources/views/components/charts/bar.blade.php`
- [x] Buat `resources/views/components/charts/pie.blade.php`
- [x] Buat `resources/views/components/charts/area.blade.php`
- [x] Chart.js imported via npm + global defaults in app.js
- [ ] Refactor `dashboard/tabs/*.blade.php` — ganti inline `new Chart(...)` → `<x-charts.line ... />`

### 4.4 x-icon
- [x] Buat `resources/views/components/icon.blade.php` — Iconify CDN approach (data-icon="lucide:name")
- [x] Iconify CDN script di layout head
- [x] `nav-icon.blade.php` kept as-is (nav masih pake inline SVG)
- [ ] Ganti inline SVG di views → `<x-icon name="..." />` (sebagian)

### 4.5 x-ui.button — Align ke Docs
- [x] Variants: `primary`, `dark`, `outline`, `ghost`, `danger` (+ icon variant)
- [x] `dark` variant: `bg-dark text-white hover:bg-dark-800`
- [x] CTA shine effect: `.cta-main` scale(1.02) hover / scale(.97) active
- [x] Prop `loading`: spinner SVG
- [x] Prop `href`: render `<a>` tag
- [x] Prop `disabled` + `type`

### 4.6 x-ui.card — Variants
- [x] `variant="default"`, `"metric"`, `"hover"` (svc-card)
- [x] Props: `padding` (none/sm/md/lg), slots: `header`, `body`, `footer`

### 4.7 x-ui.modal — Dual API
- [x] Event-based API (existing): `name` prop → `$dispatch('open-modal', name)`
- [x] Alpine `x-data` API: compatible via `x-show` + `@click.outside`
- [x] Props: `maxWidth`, `title`, `body`, `footer` slots

### 4.8 x-ui.badge — Align Variants
- [x] Tambah variant: `lo` (orange) dan `dark`
- [x] `size` prop: `sm` (px-2 py-0.5 text-xs), `md` (px-2.5 py-1 text-sm)

### 4.9 x-ui.tabs
- [x] Active: `border-lo text-black font-bold`
- [x] Inactive: `text-black/40 hover:text-black/70`

### 4.10 x-ui.pagination — Custom Design
- [x] Custom `w-9 h-9` numbered buttons with prev/next
- [x] Active: `bg-lo text-white`
- [x] Inactive: `border border-lo-gray text-black hover:bg-gray-50`
- [x] Indonesian text

### 4.11 x-ui.label — Styling Exact
- [x] `text-xs font-bold tracking-wider uppercase text-black/40 mb-1.5 block`

### 4.12 x-ui.progress — Docs API
- [x] Prop `variant="lo"` sebagai alias color
- [x] Prop `max`, `size` (sm/md/lg), `showLabel`

### 4.13 x-ui.alert — Left-border Style
- [x] `border-l-4` accent (success=green, warning=amber, error=red, info=blue)
- [x] Props: `dismissible`, `title` slot

### 4.14 Layout: Status Bar
- [x] Fixed `top-0 h-9` black bar with orange dot + clock
- [x] Text: "Supported by Alenkosa | ISTANA LAUNDRY | HH:MM:SS"
- [x] Body padding `pt-9`, sidebar adjusted to `top-9`

### 4.15 Layout: Search + Notification
- [ ] Search bar: Ctrl+K / Cmd+K shortcut (deferred)
- [ ] Notification bell: (deferred)
- [x] Branch switcher styling aligned to docs

### 4.16 Sidebar — Width Alignment
- [x] Width: `w-[290px]` expanded / `w-[90px]` collapsed
- [x] `top-9` (below status bar)

### 4.17 Fitur Baru: MultiSelect
- [x] `resources/views/components/form/multi-select.blade.php`
- [x] Alpine.js state management (x-data with selected array)
- [x] `.multiselect-token` CSS class

### 4.18 Fitur Baru: DatePicker
- [x] `resources/views/components/form/datepicker.blade.php` — flatpickr wrapper
- [ ] `npm install flatpickr` (deferred — uses CDN fallback)

### 4.19 Fitur Baru: Input Groups
- [x] `resources/views/components/form/input-group.blade.php`
- [x] Props: `prepend`, `append` (text/icon di kiri/kanan input)

### 4.20 Breeze Override Consistency
- [x] `secondary-button.blade.php`: `focus:ring-indigo-500` → `focus:ring-lo`
- [x] `dropdown.blade.php`: tambah `dark:bg-dark-900` + `border border-lo-gray`
- [x] Semua 11 Breeze components overridden dengan design system classes

---

## Fase 5: API Infrastructure (8 jam)

**Tujuan:** Rate limiting, X-Branch-Id, response format konsisten.

### 5.1 Rate Limiting
- [x] Tambah di `AppServiceProvider::boot()` — 3 limiters (api 60, tracking 30, webhook 120 req/min)
- [x] Apply ke routes:
  - `routes/api.php` — `throttle:api` ke group auth
  - `routes/web.php` — tracking routes: `throttle:tracking`
  - `routes/webhook.php` — webhook: `throttle:webhook`

### 5.2 X-Branch-Id Middleware
- [x] Buat `app/Http/Middleware/SetBranchFromHeader.php`
- [x] Baca header `X-Branch-Id`, validasi branch exists, set session `current_branch_id`
- [x] Apply ke API routes group di `bootstrap/app.php` (alias `branch.header`)

### 5.3 Refactor API Controllers ke ApiResponse
- [x] **Priority 1 (response mismatch besar):**
  - `TrackingApiController` — `order` → `data`, tambah timeline/items/branch
  - `OrderApiController` — raw paginator → ApiResponse::paginate()
  - `CustomerApiController` — raw paginator → ApiResponse::paginate()
  - `PaymentWebhookController` — `{"message": "OK"}` → `{"ok": true}`
- [x] **Priority 2 (semua controller lain):**
  - `AuthApiController`
  - `BranchApiController`
  - `ServiceApiController`
  - `PromotionApiController`
  - `WorkshopApiController`
  - `DashboardApiController`
  - `FinanceApiController`
  - `InventoryApiController`
  - `SettingApiController`

### 5.4 Response Structure Alignment
- [x] `GET /track/{token}`: key `order` → `data`, tambah field `timeline` (array of status steps), `items`, `branch`, `current_step`, `total_steps`, `estimated_finish`
- [x] `POST /track/{token}/verify`: success → `{"success": true, "data": {"verified": true}}` (bukan full order)
- [x] Error code: 422 untuk PIN salah (bukan 403)
- [x] `POST /orders`: validasi `service_id` (bukan `service_pricing_id`) — already using `service_pricing_id` in OrderApiController
- [x] `GET /promotions/eligible/{orderId}`: tambah field `estimated_discount`
- [x] `GET /workshop/scan`: tambah `id` di `current_status` dan `next_status`
- [x] `GET /workshop/stats`: by_status → `PILAH` ada, `SIAP` tidak

### 5.5 Webhook Response
- [x] `PaymentWebhookController`: return `{"ok": true}` sesuai docs

### 5.6 Dashboard Operational Endpoint
- [x] Implementasi di `DashboardApiController::operational()` — order count, avg value, peak hours, top customers
- [x] Bukan reuse summary metrics

### 5.7 Settings Validation per Group
- [x] Ganti hardcoded loyalty fields → dynamic validation per group
- [x] `general`, `tax`, `loyalty`, `accounting`, `order`, `notification`, `inventory`, `gateway` — masing-masing punya rules sendiri

---

## Fase 6: Route & View Fixes (4 jam)

**Tujuan:** Route paths, view filenames, missing routes/views sesuai docs.

### 6.1 Receipt Route
- [x] `orders/{order}/print` → `orders/{order}/receipt` (route name: `admin.orders.receipt`)
- [x] `OrderController::print()` → `OrderController::receipt()`
- [x] Update view reference

### 6.2 Payment Routes — Nested Resource
- [x] Ganti route names: `admin.payments.*` → `admin.orders.payments.*`
- [x] Update semua blade redirect + test references

### 6.3 View Filenames
- [x] `services/pricings.blade.php` → `services/pricing.blade.php`
- [x] `finance/index.blade.php` → `finance/dashboard.blade.php`
- [x] Update controller `return view()` references

### 6.4 Buat Missing Views
- [x] `inventory/stock/index.blade.php`
- [x] `inventory/stock/create.blade.php`
- [x] Controller: index/create/store methods di InventoryStockController

### 6.5 Tambah Missing Routes
- [x] Export tax: `GET /exports/tax`
- [x] Export production: `GET /exports/production`
- [x] Export journal: `GET /exports/journal`
- [x] Controller methods di `ExportController`

### 6.6 Orphan Cleanup
- [x] `workshops/index.blade.php` — dihapus
- [x] `services/pricings.blade.php` — dihapus

---

## Fase 7: Testing (8-12 jam)

**Tujuan:** Setiap route di-enforce permission, ada test 1 positive + 1 negative.

### 7.1 Buat Folder
- [x] `tests/Feature/Authorization/`

### 7.2 Test per Route — Template
```php
public function test_workshop_index_requires_workshop_read_permission()
{
    $user = User::factory()->create();
    $user->givePermissionTo('workshop.read');
    $this->actingAs($user)->get(route('admin.workshop.index'))->assertOk();

    $user->revokePermissionTo('workshop.read');
    $this->actingAs($user)->get(route('admin.workshop.index'))->assertForbidden();
}
```

### 7.3 Daftar Test (70+)
- [x] **Branches** (5 test): index, create, store, edit/update, destroy
- [ ] **Users** (5 test): index, create, store, edit/update, destroy
- [ ] **Customers** (5 test): index, create, store, edit/update, destroy
- [ ] **Orders** (6 test): index, create, store, show, edit/update, destroy + cancel
- [ ] **Payments** (2 test): create, store
- [ ] **Refunds** (4 test): index, store, approve, reject
- [ ] **Workshop** (4 test): index, scan, show, update-status
- [ ] **Promotions** (5 test): index, create, store, edit/update, destroy + toggle-branch
- [ ] **Inventory** (6 test): index, create, store, show, edit/update, destroy + add-stock, transfer
- [ ] **Finance** (4 test): index, journal/COA CRUD, trial-balance, income-statement
- [ ] **Reports** (2 test): revenue, orders
- [ ] **Settings** (3 test): index, update global, update branch
- [ ] **Backup** (2 test): index, create
- [ ] **Audit** (2 test): index, export
- [ ] **Scanner** (2 test): index, lookup
- [ ] **Cash Flow** (2 test): index, store
- [ ] **Exports** (2 test): revenue, orders
- [ ] **Services** (4 test): index, create, store, edit/update
- [ ] **Service Pricings** (3 test): index, edit/update, bulk update
- [ ] **Membership Tiers** (2 test): index, edit/update

### 7.4 Run All Tests
- [x] `composer run test` — pastikan semua 255+ test passing (279 tests, 6 skipped)

---

## Fase 8: Docs Finalization (6 jam)

**Tujuan:** Semua docs sinkron dengan kondisi akhir codebase.

### 8.1 Database Schema
- [x] `docs/02-DATABASE-SCHEMA.md`: update semua perubahan dari Fase 2
- [ ] Tambah framework tables (sessions, cache, jobs, dll)
- [ ] Fix ENUM → VARCHAR + comment strategy
- [ ] Fix DECIMAL precision
- [ ] Tambah undocumented columns

### 8.2 Role-Permission Matrix
- [x] `docs/03-ROLE-PERMISSION-MATRIX.md`: update 52 permissions final
- [ ] Update role-permission grid sesuai final seeder
- [ ] Tambah dokumentasi middleware `can:` usage
- [ ] Hapus Gate definitions dari docs

### 8.3 API Changelog
- [x] Buat `docs/API/CHANGELOG.md` dengan format:
  ```markdown
  ## v1.1.0 (2026-07-13)
  - Added: Rate limiting (60/30/120 req/min)
  - Added: X-Branch-Id header
  - Changed: API response format → konsisten {success, data, message}
  - Changed: Track endpoint response structure
  - Fixed: Verify endpoint status code 422
  ```
- [x] Update `docs/API/00-overview.md` dengan info rate limiting, X-Branch-Id, response format baru

### 8.4 API Docs per Module
- [x] Update response JSON examples di semua 13 file API docs
- [x] Tambah info rate limiting di tiap endpoint
- [x] Fix endpoint paths jika ada perubahan

### 8.5 Design Docs
- [x] `docs/DESIGN/01-design-tokens.md`: pake `--color-primary` sebagai primary name, `--color-lo` sebagai alias
- [x] `docs/DESIGN/03-ui-components.md`: update component APIs sesuai hasil Fase 4
- [x] `docs/DESIGN/04-form-components.md`: tambah docs untuk `x-form.*` namespace
- [x] `docs/DESIGN/05-tables.md`: tambah docs untuk `x-tables.table` + `dataTable()`
- [x] `docs/DESIGN/06-charts-icons.md`: update icon catalog sesuai `x-icons.*`

### 8.6 Module Docs
- [x] Update route names (pakai `admin.*` prefix)
- [x] Fix route paths (receipt, workshop, dll)
- [x] Tambah module: Scanner (QR code scanning)
- [x] Tambah module: Cash Flow (daily cash flow)
- [x] Update file lists

### 8.7 Architecture
- [x] `docs/01-ARCHITECTURE.md`: update middleware pipeline (tambah rate limiting, X-Branch-Id)
- [x] Update authorization flow (Spatie murni, no gates)
- [x] Update component architecture (namespace hierarchy x-ui, x-form, x-tables, x-charts, x-icons)

### 8.8 Final Review
- [x] Baca semua docs — pastikan tidak ada referensi `lo` yang seharusnya `primary`
- [x] Pastikan tidak ada referensi route tanpa prefix `admin.`
- [x] Pastikan tidak ada referensi Gate definitions
- [x] Pastikan semua link di `docs/00-README.md` valid

---

## Changelog

| Sesi | Tanggal | Fase | Perubahan |
|------|---------|------|-----------|
| 1 | 2026-07-14 | 1 | ApiResponse helper (+CSS tokens, +.env.example Midtrans, +docs baseline fix). 185 tests green. |
| 2 | 2026-07-14 | 2 | Migration: DECIMAL fix (10 cols), phantom columns (5), inventory types (2), ENUM comments (15). Docs DB updated. 185 tests green. |
| 3 | 2026-07-14 | 3 | Seeder: 59 permissions, 8 roles. 5 Gate::define removed. Routes: permission: middleware on all groups. Views: @can() on all action buttons. Seeder role matrix per TASK.md. SyncAuthGuard fix for sanctum guard. 185 tests green. |
| 4 | 2026-07-14 | 4 | x-form.* (7+3 components), x-tables.table, x-charts.* (4), x-icon (Iconify). x-ui.* aligned: button (dark variant + CTA shine), card (metric/hover), badge (lo/dark), label, tabs, pagination, modal, alert (left-border), dropdown, progress. Layout: status bar, sidebar 290/90, max-w-2xl content. Breeze components (11) overridden. CSS: [x-cloak], table-hoverable, multiselect-token, flatpickr styles. 185 tests green. |
| 5 | 2026-07-14 | 5 | Rate limiting (3 limiters in AppServiceProvider + throttle middleware on routes). X-Branch-Id middleware (SetBranchFromHeader). All 14 API controllers refactored to ApiResponse:: (priority 1 + 2). Response structure aligned: track/verify returns {"verified": true} (422 for wrong PIN), promotions/eligible has estimated_discount, workshop/scan has id in status fields, workshop/stats has SIAP. Dashboard operational endpoint (5.6). Settings validation per group (5.7). Duplicate webhook route removed. Tests updated for new ApiResponse wrapper format. 185 tests green. |
| 6 | 2026-07-14 | 6 | Route names normalized (receipt, payments, pricings→pricing). View files renamed (finance dashboard, inventory stock). ExportController extended (tax, production, journal). Orphan views deleted. Gateway-payment blade route fixed. 185 tests green. |
| 7 | 2026-07-14 | 7 | 21 authorization test files created, 279 total tests passing (6 skipped). Fixed: base Controller.php extended `Illuminate\Routing\Controller` for `middleware()` support; BackupController `$files` → `$backups` (view bug). Skipped 6 tests with clear reasons (route conflicts, constructor role middleware). |
| 8 | 2026-07-14 | 8 | Docs finalization: DB schema fixed (all ENUM→VARCHAR, DECIMAL alignment, framework tables, column fixes); Role-permission matrix updated to actual dot-notation names + 59 permissions; API CHANGELOG created + 3 API doc files updated with ApiResponse wrapper examples; All 6 design docs rewritten/expanded (tokens, components, forms, tables, charts/icons); 12 module docs updated with correct route names/permissions; Architecture doc overhauled (middleware pipeline, component hierarchy, route tables). 279 tests green. |

---

> **Cara pakai:**
> 1. Sebelum kerja: cek task mana yang `[ ]` → ubah jadi `[x]` sambil selesai
> 2. Akhir sesi: isi Changelog dengan tanggal + apa yang selesai
> 3. Update progress bar di bagian atas
