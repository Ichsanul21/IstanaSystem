# Architecture

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 13 + PHP 8.5+ |
| **Frontend** | Blade + Alpine.js 3 |
| **CSS** | Tailwind CSS v4 (via Vite, PostCSS plugin) |
| **Database** | SQLite (dev), MySQL 8 / PostgreSQL 15 (prod) |
| **Auth** | Laravel Breeze (Blade) + Spatie Permission |
| **Payment** | Midtrans Snap |
| **Charts** | Chart.js (CDN) |
| **Export** | Laravel Excel + DomPDF |
| **Backup** | Spatie Laravel Backup |

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
│       │   └── ui/
│       │       ├── alert.blade.php
│       │       ├── badge.blade.php
│       │       ├── button.blade.php
│       │       ├── card.blade.php
│       │       ├── input.blade.php
│       │       ├── label.blade.php
│       │       ├── modal.blade.php
│       │       ├── pagination.blade.php
│       │       ├── select.blade.php
│       │       ├── table.blade.php
│       │       ├── tabs.blade.php
│       │       └── textarea.blade.php
│       ├── auth/
│       ├── branches/
│       ├── cash-flow/
│       ├── customers/
│       ├── dashboard/
│       │   ├── index.blade.php
│       │   ├── metrics.blade.php
│       │   └── charts.blade.php
│       ├── exports/
│       ├── finance/
│       ├── inventory/
│       ├── orders/
│       ├── payments/
│       ├── pos/
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

## Data Flow

```
User (Browser) → Blade View → Alpine.js
                     ↓
               HTTP Request → CSRF + Middleware
                     ↓
               Controller → FormRequest
                     ↓
               Service Layer (business logic)
                     ↓
               Eloquent Model → Database
                     ↓
               Response → Blade View
```

## Branch Scoping

- `SetBranchContext` middleware sets `session('branch_id')` on every request
- Branch switcher for Developer/Owner/Super Admin
- Branch Admin/CS/Cashier bound to assigned branch
- `HasBranchScope` trait auto-filters queries by `current_branch_id`

## Dark Mode

- Alpine.js store + `localStorage` persistence
- `.dark` class on `<html>` element
- Tailwind `dark:` variant

## Route Structure

| Group | Prefix | Middleware |
|-------|--------|-----------|
| Admin | `/admin` | `auth`, `verified`, `branch` |
| API | `/api/v1` | `auth:sanctum` |
| Webhook | `/api/webhook/midtrans` | None (CSRF excluded) |
| Tracking | `/track/{token}` | None (public) |
