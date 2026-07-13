# Istana Laundry System — AGENTS.md

## Stack
- **Framework**: Laravel 13.19.0 + Blade + Vite 7
- **CSS**: Tailwind CSS v4 (`@tailwindcss/vite` plugin, no `tailwind.config.js`, no `postcss` Tailwind plugin)
- **JS**: Alpine.js 3 (stores in `resources/js/stores/` — theme.js for dark mode, sidebar.js for collapse)
- **Auth**: Laravel Breeze (Blade) v2.4.2
- **Roles**: Spatie Permission v8.3.0 (8 roles, 37 permissions) — seeded by `RolePermissionSeeder`
- **Payments**: Midtrans v2.6.0 (via `PaymentWebhookController`)
- **PDF**: DomPDF dev-master (config NOT published — `config/dompdf.php` missing)
- **Excel**: Laravel Excel 3.1.69
- **Backup**: Spatie Backup 10.3.0
- **Charts**: Chart.js (loaded via CDN in views, not bundled)
- **DB**: SQLite (dev), MySQL/PostgreSQL (prod)

## Commands
```bash
composer run dev          # Laravel dev: serve + queue + logs + vite concurrently
composer run test         # php artisan config:clear && php artisan test
npm run dev               # Vite dev (hot reload)
npm run build             # Vite production build
php artisan serve         # Laravel dev server only
php artisan migrate:fresh --seed
php artisan route:list --except-vendor
php artisan optimize:clear
```

## Architecture

### Route groups (from `routes/web.php`)
- `GET /track/{token}`, `POST /track/{token}/verify` — public tracking (no auth)
- **`admin.*`** — all authenticated routes under `Route::middleware(['auth', 'verified'])->name('admin.')`
  - Inside: `branch` middleware group (`SetBranchContext`) — required for most features
  - Resourceful: `branches`, `users`, `customers`, `orders`, `promotions`, `inventory`
  - Nested: `orders/{order}/payments`, `orders/{order}/refunds`
  - Prefix groups: `finance.*` (6 routes), `reports.*` (6 routes)
- `api/v1/*` — public tracking, auth:sanctum for orders/customers CRUD
- `api/webhook/midtrans` — POST only, CSRF excluded, no `webhook.signature` middleware applied (registered but unused)

### API routes (`routes/api.php`)
- `GET /api/v1/track/{token}`, `POST /api/v1/track/{token}/verify` — public
- `auth:sanctum` — `apiResource` orders + customers, `PUT orders/{order}/status`
- `GET /api/customers/search?q=` — registered (used by POS view)

### Middleware (registered in `bootstrap/app.php`)
- `branch` — `SetBranchContext` (stores `current_branch_id` in session)
- `role` — `CheckRole` (comma-separated roles, e.g. `role:Developer,Super Admin`)
- `webhook.signature` — `VerifyWebhookSignature` (registered but unused in routes)

### Branch scoping
- `SetBranchContext` middleware → session `current_branch_id` → `currentBranchId()` helper
- `HasBranchScope` trait → `scopeForBranch($id)` + `scopeForCurrentBranch()`
- `currentBranchId()` can return `null` (unauthenticated / no branch set) — guard before queries

### Conventions
- **Route names**: All admin routes prefixed `admin.*` (e.g. `admin.dashboard`, `admin.orders.index`)
- **Controller redirects**: MUST use `route('admin.*')` — bare `route('users.index')` will throw `RouteNotFoundException`
- **Views**: Use `<x-layouts.admin>` component syntax with `$slot` + `$header` sections (NOT `layouts/app.blade.php` for admin)
- **UI components**: `<x-ui.button>`, `<x-ui.card>`, `<x-ui.badge>`, `<x-ui.table>`, `<x-ui.input>`, `<x-ui.select>`, `<x-ui.modal>`, `<x-ui.alert>`, `<x-ui.tabs>`, `<x-ui.pagination>`, `<x-ui.label>`, `<x-ui.textarea>`
- **Brand**: `#FF6B00` (primary-orange), `#000000` (dark), `#E5E5E5` (lo-gray), Inter font (300-900)
- **Models**: No `HasFactory` trait (Laravel 13 default)
- **No comments** in Blade templates
- **Seeders** are idempotent (`firstOrCreate` / `updateOrCreate`)

### Key enum values (for view validation alignment)
- `ProductionStatus`: `received`, `washed`, `dried`, `ironed`, `packed`, `ready_for_pickup`, `picked_up`, `cancelled`
- `OrderStatus`: `draft`, `pending`, `processing`, `completed`, `cancelled`
- Views use Indonesian labels/values — enum values above are what `updateStatus()` validates against

## View file layout
Views are at `resources/views/{module}/{action}.blade.php`. Notable: `dashboard.blade.php` is at root (not `dashboard/index.blade.php`).
