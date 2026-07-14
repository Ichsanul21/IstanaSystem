# Architecture

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 13 + PHP 8.5+ |
| **Frontend** | Blade + Alpine.js 3 |
| **CSS** | Tailwind CSS v4 (`@tailwindcss/vite` plugin, no `tailwind.config.js`) |
| **Database** | SQLite (dev), MySQL 8 / PostgreSQL 15 (prod) |
| **Auth** | Laravel Breeze (Blade) v2.4.2 + Spatie Permission v8.3.0 |
| **Payment** | Midtrans Snap v2.6.0 |
| **Charts** | Chart.js (CDN, loaded in views — not bundled) |
| **Export** | Laravel Excel 3.1.69 + DomPDF dev-master |
| **Backup** | Spatie Laravel Backup 10.3.0 |

## Folder Structure

```
istana-laundry/
├── app/
│   ├── Console/
│   ├── Enums/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── V1/
│   │   │   └── Web/
│   │   ├── Middleware/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Models/
│   ├── Observers/
│   ├── Providers/
│   ├── Services/
│   └── Traits/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   │   ├── stores/
│   │   │   ├── theme.js
│   │   │   └── sidebar.js
│   │   └── app.js
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   └── admin.blade.php
│       ├── components/
│       │   ├── ui/            ← Base UI primitives
│       │   │   ├── alert.blade.php
│       │   │   ├── badge.blade.php
│       │   │   ├── button.blade.php
│       │   │   ├── card.blade.php
│       │   │   ├── dropdown.blade.php
│       │   │   ├── input.blade.php
│       │   │   ├── label.blade.php
│       │   │   ├── modal.blade.php
│       │   │   ├── pagination.blade.php
│       │   │   ├── progress.blade.php
│       │   │   ├── select.blade.php
│       │   │   ├── table.blade.php
│       │   │   ├── tabs.blade.php
│       │   │   └── textarea.blade.php
│       │   ├── form/          ← Form components
│       │   │   ├── checkbox.blade.php
│       │   │   ├── datepicker.blade.php
│       │   │   ├── error.blade.php
│       │   │   ├── input.blade.php
│       │   │   ├── input-group.blade.php
│       │   │   ├── label.blade.php
│       │   │   ├── multi-select.blade.php
│       │   │   ├── radio.blade.php
│       │   │   ├── select.blade.php
│       │   │   └── textarea.blade.php
│       │   ├── tables/        ← Advanced table components
│       │   │   └── table.blade.php
│       │   ├── charts/        ← Chart.js wrapper components
│       │   │   ├── area.blade.php
│       │   │   ├── bar.blade.php
│       │   │   ├── line.blade.php
│       │   │   └── pie.blade.php
│       │   ├── dashboard/     ← Dashboard widget partials
│       │   │   └── partials/
│       │   ├── icons/
│       │   ├── icon.blade.php ← Iconify CDN icon component
│       │   └── (Breeze components: dropdown, modal, buttons, etc.)
│       ├── auth/
│       ├── audit/
│       ├── branches/
│       ├── cash-flow/
│       ├── customers/
│       ├── dashboard/
│       │   ├── tabs/
│       │   └── partials/
│       ├── exports/
│       ├── finance/
│       ├── inventory/
│       ├── membership-tiers/
│       ├── orders/
│       ├── payments/
│       ├── pos/
│       ├── profile/
│       ├── promotions/
│       ├── refunds/
│       ├── reports/
│       ├── services/
│       ├── settings/
│       ├── tracking/
│       ├── users/
│       └── workshop/
├── routes/
│   ├── web.php
│   ├── api.php
│   ├── webhook.php
│   ├── auth.php
│   └── console.php
├── storage/
├── tests/
└── docs/
```

## Middleware Pipeline

### Registered Middleware Aliases (`bootstrap/app.php`)

| Alias | Class | Purpose |
|-------|-------|---------|
| `auth.sync` | `SyncAuthGuard` | Syncs auth guard between web + API contexts |
| `branch` | `SetBranchContext` | Sets `current_branch_id` in session from DB/user |
| `branch.header` | `SetBranchFromHeader` | Sets branch from `X-Branch-Id` header (API) |
| `permission` | `Spatie\PermissionMiddleware` | Checks Spatie permission (comma-separated) |
| `role` | `Spatie\RoleMiddleware` | Checks Spatie role (comma-separated) |
| `webhook.signature` | `VerifyWebhookSignature` | Verifies webhook HMAC signature (registered, not yet applied in routes) |

### Middleware Pipeline Per Request Group

```
Web (Admin):
  CSRF (VerifyCsrfToken) → auth → verified → branch → permission → Controller

Web (Tracking - public):
  CSRF → Controller (no auth)

API (authenticated):
  auth:sanctum → auth.sync → throttle:api → permission → Controller

API (Tracking - public):
  throttle:tracking → Controller

Webhook:
  CSRF excluded → webhook.signature → Controller
```

### Rate Limiting

- `throttle:5,15` — Tracking verification (`POST /track/{token}/verify`)
- `throttle:tracking` — API tracking endpoints
- `throttle:api` — All authenticated API endpoints

## Authorization Flow

Authorization is handled **exclusively via Spatie Permission middleware** — no Laravel Gates or Policies are used.

```
Route definition:
  ->middleware('permission:order.read|order.create')

Middleware pipeline:
  1. SetBranchContext → stores current_branch_id in session
  2. PermissionMiddleware → checks authenticated user has at least one of the listed permissions
  3. If denied → 403 Forbidden

Role checks (rare, used only for role-gating):
  ->middleware('role:Developer,Super Admin')
```

**Permission naming convention:** `{domain}.{action}` — e.g. `order.read`, `finance.write`, `inventory.create`

## Branch Scoping

- `SetBranchContext` middleware sets `session('current_branch_id')` from the authenticated user's assigned branch
- `SetBranchFromHeader` middleware reads `X-Branch-Id` HTTP header for API requests
- Branch switcher available for Developer/Owner/Super Admin (`POST /admin/branch/switch/{branch}`)
- Branch Admin/CS/Cashier bound to their assigned branch (cannot switch)
- `HasBranchScope` trait auto-filters queries by `current_branch_id` via `scopeForCurrentBranch()` and `scopeForBranch($id)`
- `currentBranchId()` helper returns `null` when no branch is set — always guard before queries

## Component Architecture

All Blade components use anonymous component syntax (no backing class).

### Namespace Hierarchy

| Namespace | Usage | Components |
|-----------|-------|------------|
| `x-ui.*` | Base UI primitives | `alert`, `badge`, `button`, `card`, `dropdown`, `input`, `label`, `modal`, `pagination`, `progress`, `select`, `table`, `tabs`, `textarea` |
| `x-form.*` | Form field components | `checkbox`, `datepicker`, `error`, `input`, `input-group`, `label`, `multi-select`, `radio`, `select`, `textarea` |
| `x-tables.*` | Advanced tables | `table` (with `hoverable`, `striped` props) |
| `x-charts.*` | Chart.js CDN wrappers | `area`, `bar`, `line`, `pie` |
| `x-icon` | Iconify CDN icons | Single component — accepts `name` prop (e.g. `lucide:download`) |
| `x-dashboard.*` | Dashboard widgets | `partials/metric-card`, `partials/chart-card`, `partials/recent-orders`, `partials/low-stock-alert` |

### Breeze / Legacy Components (root-level)

| Component | Purpose |
|-----------|---------|
| `<x-application-logo>` | App logo |
| `<x-auth-session-status>` | Auth status flash message |
| `<x-primary-button>` | Primary CTA (Breeze) |
| `<x-secondary-button>` | Secondary CTA (Breeze) |
| `<x-danger-button>` | Destructive CTA (Breeze) |
| `<x-dropdown>` / `<x-dropdown-link>` | Dropdown menus (Breeze) |
| `<x-modal>` | Modal dialog (Breeze) |
| `<x-nav-link>` / `<x-responsive-nav-link>` | Navigation links (Breeze) |
| `<x-input-label>` / `<x-input-error>` / `<x-text-input>` | Form helpers (Breeze) |

## Dark Mode

- Alpine.js store + `localStorage` persistence (`resources/js/stores/theme.js`)
- `.dark` class on `<html>` element
- Tailwind `dark:` variant throughout

## Route Structure

| Group | Prefix | Middleware | Route Name Prefix |
|-------|--------|-----------|-------------------|
| Admin (no branch) | `/admin` | `auth`, `verified`, `permission:*` | `admin.*` |
| Admin (branch-scoped) | `/admin` | `auth`, `verified`, `branch`, `permission:*` | `admin.*` |
| API (public) | `/api/v1` | None | — |
| API (authenticated) | `/api/v1` | `auth:sanctum`, `auth.sync`, `throttle:api`, `permission:*` | — |
| API (POS search) | `/api` | `auth:sanctum`, `auth.sync` | — |
| Webhook | `/api/webhook` | `web`, `webhook.signature`, CSRF excluded | `webhook.*` |
| Tracking (public) | `/track/{token}` | None | `tracking.*` |

### Admin Route Sub-Groups

| Sub-Group | Path Pattern | Extra Middleware | Permission Example |
|-----------|-------------|-----------------|-------------------|
| Settings (global) | `/admin/settings` | `permission:settings.read\|settings.update\|edit_global_settings` | `settings.read` |
| Gateway Config (global) | `/admin/settings/gateway` | `permission:manage_gateway_config` | `manage_gateway_config` |
| Audit / Activity Logs (global) | `/admin/audit`, `/admin/activity-logs` | `permission:view_activity_logs` | `view_activity_logs` |
| Backup (global) | `/admin/backup/*` | `permission:run_backup\|view_system_info` | `run_backup` |
| Dashboard | `/admin`, `/admin/dashboard` | `branch` | (none — auth only) |
| Branch Switch | `/admin/branch/switch/{branch}` | `branch`, `permission:switch_branch` | `switch_branch` |
| Branches (CRUD) | `/admin/branches/*` | `branch`, `permission:branch.*` | `branch.create`, `branch.read`, etc. |
| Users (CRUD) | `/admin/users/*` | `branch`, `permission:user.*` | `user.read`, `user.create`, etc. |
| Customers (CRUD) | `/admin/customers/*` | `branch`, `permission:customer.*` | `customer.read`, `customer.create`, etc. |
| POS | `/admin/pos` | `branch`, `permission:order.create` | `order.create` |
| Orders (CRUD) | `/admin/orders/*` | `branch`, `permission:order.*` | `order.read`, `order.create`, etc. |
| Orders Receipt | `/admin/orders/{order}/receipt` | `branch`, `permission:order.read` | `order.read` |
| Payments | `/admin/orders/{order}/payments/*` | `branch`, `permission:payment.*` | `payment.create`, `payment.read` |
| Refunds | `/admin/refunds/*` | `branch`, `permission:*_refund` | `process_refund`, `approve_refund` |
| Workshop | `/admin/workshop/*` | `branch`, `permission:workshop.*` | `workshop.read`, `workshop.scan`, `workshop.update_status` |
| Promotions (CRUD) | `/admin/promotions/*` | `branch`, `permission:promotion.*` | `promotion.read`, `promotion.create`, etc. |
| Inventory (CRUD) | `/admin/inventory/*` | `branch`, `permission:inventory.*` | `inventory.read`, `inventory.create`, etc. |
| Stock Ops | `/admin/inventory/{item}/add-stock`, `/stock-out`, etc. | `branch`, `permission:stock_in\|stock_out` | `stock_in`, `stock_out` |
| Services (CRUD) | `/admin/services/*` | `branch`, `permission:view_services\|create_services\|edit_services` | |
| Service Pricing | `/admin/services/pricing/*` | `branch`, `permission:edit_service_pricing` | `edit_service_pricing` |
| Finance | `/admin/finance/*` | `branch`, `permission:finance.read\|create_manual_journal\|manage_accounting_periods\|manage_expenses` | |
| Branch Settings | `/admin/settings/branches/{branch}` | `branch`, `permission:edit_branch_settings` | `edit_branch_settings` |
| Reports | `/admin/reports/*` | `branch`, `permission:report.read\|view_financial_reports` | |
| Exports | `/admin/exports/*` | `branch`, `permission:export_data` | `export_data` |
| Daily Cash Flow | `/admin/cash-flow` | `branch`, `permission:finance.read\|create_manual_journal` | |
| Scanner | `/admin/scanner` | `branch`, `permission:workshop.scan` | `workshop.scan` |
| Membership Tiers | `/admin/membership-tiers/*` | `branch`, `permission:manage_tiers` | `manage_tiers` |
