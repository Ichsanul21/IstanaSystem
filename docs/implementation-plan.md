# Implementation Plan

## Phases Overview

| Phase | Focus | Duration (Est.) |
|-------|-------|-----------------|
| 0 | Project Setup: Laravel + Breeze + Tailwind + Alpine + Spatie | 1 day |
| 1 | Database: All migrations + Models + Relations | 1 day |
| 2 | Auth: Login, Roles, Permissions, Branch Scope | 1 day |
| 3 | Design: Layout (sidebar/header), UI components, Dark mode | 2 days |
| 4 | Settings Engine: Settings table, helpers, default seeder | 0.5 day |
| 5 | Branch & Workshop CRUD + Branch Switcher | 1 day |
| 6 | Master Data: Services + Service Pricing | 1 day |
| 7 | POS: Orders flow, Payment, Receipt | 2 days |
| 8 | CRM: Customers, Tiers, Points | 2 days |
| 9 | Promotions Engine | 1 day |
| 10 | Workshop: QR scan, Status tracking, WA modal | 2 days |
| 11 | Finance: COA, Journal, Auto-posting, Tax | 2 days |
| 12 | Inventory: FIFO, Batches, Transactions | 1.5 days |
| 13 | Dashboard & Reports per role | 2 days |
| 14 | Midtrans Gateway | 1 day |
| 15 | Customer Tracking Page | 0.5 day |
| 16 | Audit, Export, Backup | 1 day |
| 17 | Testing + Bug Fixes | 2 days |
| 18 | Deployment + Documentation Polish | 1 day |

**Total estimated: ~24 days (full-time)**

## Phase Details

### Phase 0: Project Setup

```bash
composer create-project laravel/laravel istana-laundry "^13.0"
composer require laravel/breeze --dev
php artisan breeze:install blade
composer require spatie/laravel-permission
```

- Configure Vite + Tailwind CSS v4 + Alpine.js
- Setup `.env` with database connection
- Configure tailwind theme with Istana brand colors
- Run `php artisan install:breeze` for auth scaffolding

### Phase 1: Database

- Create all migration files (30+ tables)
- Define all Models with relationships
- Create enums (OrderStatus, ProductionStatus, PaymentMethod, TaxRegime)
- Create traits (HasBranchScope, HasQrToken, GeneratesOrderNumber, LogsActivity)
- Create exceptions (InsufficientStockException, InvalidStatusTransitionException)
- Run migrations

### Phase 2: Auth

- Configure Spatie Permission with 8 roles
- Create `SetBranchContext` middleware
- Create `HasBranchScope` trait
- Create User controller with role assignment
- Create role/permission seeder
- Implement branch scope in queries

### Phase 3: Design System (TailAdmin → Blade)

- Create admin layout (`layouts/admin.blade.php`)
- Create sidebar component with Alpine store
- Create header component with theme toggle
- Create all UI Blade components (button, card, modal, alert, etc.)
- Create form components (input, select, textarea)
- Create table components
- Create chart components (Chart.js)
- Create SVG icon components
- Implement dark mode via Alpine store + localStorage
- Copy animation styles from landing page

### Phase 4-16: Module Implementation

Follow each module's documentation for implementation. Each module includes:
- Migration file
- Model with relationships
- Service class with business logic
- Controller with CRUD
- Blade views
- Routes

### Phase 17: Testing

- Write unit tests for all service classes
- Write feature tests for critical flows (order, payment, refund)
- Test role-based access
- Browser test (Dusk) for POS + workshop scan

### Phase 18: Deployment

- Configure production `.env`
- Build assets (`npm run build`)
- Cache config/routes/views
- Setup Nginx/Apache
- Manual backup

## Parallel Work

Tasks that can be done in parallel:

| Parallel Group | Modules |
|---------------|---------|
| Group A | Phase 0 + 1 (setup + database) |
| Group B | Phase 2 + 4 + 5 (auth + settings + branch) |
| Group C | Phase 3 (design system — can overlap with B) |
| Group D | Phase 6 + 7 + 8 (master data + POS + CRM) |
| Group E | Phase 9 + 10 (promotions + workshop) |
| Group F | Phase 11 + 12 (finance + inventory) |
| Group G | Phase 13 + 14 + 15 + 16 (dashboard + gateway + tracking + audit) |
| Group H | Phase 17 + 18 (testing + deploy) |
