# Database Schema

## Overview

**Total Tables:** 30+
**Engine:** InnoDB (MySQL) / PostgreSQL
**Charset:** utf8mb4 (MySQL) / UTF-8 (PostgreSQL)

> **Note on column types:** All columns that represent enumerated values are stored as `VARCHAR`
> with a `comment` listing allowed values. App-level validation is enforced via PHP enum classes.
> For example, `VARCHAR(30)` with comment `'draft, pending, processing, completed, cancelled'`.

## Entity Relationship Summary

```
Workshops ──< Branches ──< Users
                │
                ├──< Customers
                ├──< Orders ──< OrderItems ──< ProductionStatuses (pivot: order_item_status_logs)
                │       └──< Payments ──< GatewayPayments
                │       └──< Refunds
                │       └──< PromotionUsages
                │       └──< LoyaltyPointsTransactions
                │
                ├──< Services ──< ServicePricings
                ├──< Promotions ──< PromotionBranches
                ├──< InventoryItems ──< InventoryBatches ──< InventoryTransactions
                ├──< Settings
                ├──< DailyCashFlows
                │
Customers ──< MembershipTiers
Customers ──< LoyaltyPointsTransactions

Global (cross-branch):
  ├── ChartOfAccounts ──< JournalEntries ──< JournalEntryLines
  ├── AccountingPeriods
  ├── TaxConfigurations ──< TaxLogs
  ├── ActivityLogs
  └── GatewayConfigurations
```

## Table Definitions

### 1. `workshops`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| code | VARCHAR(20) UNIQUE | WSH-001 |
| name | VARCHAR(150) | |
| address | TEXT | |
| phone | VARCHAR(20) | |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps + soft_deletes | | |

### 2. `branches`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| code | VARCHAR(20) UNIQUE | CAB-001 |
| name | VARCHAR(150) | |
| workshop_id | BIGINT FK → workshops(id) | Nullable for future |
| address | TEXT | |
| phone | VARCHAR(20) | |
| opening_time | TIME | 08:00 |
| closing_time | TIME | 21:00 |
| daily_capacity | INT | Max orders per day |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps + soft_deletes | | |

### 3. `users`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| name | VARCHAR(100) | |
| email | VARCHAR(100) UNIQUE | |
| password | VARCHAR(255) | Bcrypt |
| phone | VARCHAR(20) NULL | |
| photo | VARCHAR(255) NULL | |
| branch_id | BIGINT FK → branches(id) | NULL for central roles |
| is_active | BOOLEAN | DEFAULT TRUE |
| is_protected | BOOLEAN | DEFAULT FALSE (Developer) |
| email_verified_at | TIMESTAMP NULL | |
| remember_token | VARCHAR(100) NULL | |
| last_login_at | TIMESTAMP NULL | |
| timestamps + soft_deletes | | |

### 4. `model_has_roles` (Spatie)

Spatie Permission pivot.

### 5. `model_has_permissions` (Spatie)

Spatie Permission pivot.

### 6. `customers`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| code | VARCHAR(20) UNIQUE | CUS-00001 |
| branch_id | BIGINT FK → branches(id) | Assigned branch |
| name | VARCHAR(100) | |
| phone | VARCHAR(20) NULL | |
| pin | VARCHAR(6) NULL | Tracking PIN |
| email | VARCHAR(100) NULL | |
| address | TEXT NULL | |
| id_card_number | VARCHAR(30) NULL | KTP |
| birth_date | DATE NULL | |
| gender | VARCHAR(1) NULL | Stored as VARCHAR — app-level validation via enum classes \| Allowed: L, P |
| is_member | BOOLEAN | DEFAULT FALSE |
| membership_tier_id | BIGINT FK → membership_tiers(id) | NULL |
| total_points | BIGINT | DEFAULT 0 |
| total_purchase | DECIMAL(15,2) | DEFAULT 0 |
| total_orders | INT | DEFAULT 0 |
| last_order_at | TIMESTAMP NULL | |
| join_date | DATE | CURRENT_DATE |
| notes | TEXT NULL | |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps + soft_deletes | | |

### 7. `membership_tiers`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| name | VARCHAR(50) | Bronze/Silver/Gold/Platinum |
| level | INT | 1–4 |
| min_points | INT | 0/500/1500/5000 |
| color | VARCHAR(7) NULL | Hex color code |
| discount_percent | DECIMAL(5,2) | DEFAULT 0 |
| discount_per_order | DECIMAL(15,2) | DEFAULT 0 |
| free_delivery | BOOLEAN | DEFAULT FALSE |
| priority_service | BOOLEAN | DEFAULT FALSE |
| birthday_voucher | DECIMAL(15,2) | DEFAULT 0 |
| benefits | TEXT NULL | |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps | | |

### 8. `loyalty_points_transactions`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| customer_id | BIGINT FK → customers(id) | |
| order_id | BIGINT FK → orders(id) | NULL |
| points | BIGINT | + earn / - redeem |
| balance_after | INT | NULL |
| type | VARCHAR(20) | Stored as VARCHAR — app-level validation via enum classes \| Allowed: earn, redeem, expire, adjust |
| description | VARCHAR(255) NULL | |
| expiry_date | TIMESTAMP NULL | |
| expired_at | TIMESTAMP NULL | |
| created_by | BIGINT FK → users(id) | NULL |
| timestamps | | |

### 9. `services`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| code | VARCHAR(20) UNIQUE | CK, CB, ST, EXP, SL, LP, KP, SF |
| name | VARCHAR(150) | Cuci Kering, dll |
| unit | VARCHAR(10) | Stored as VARCHAR — app-level validation via enum classes \| Allowed: kg, pcs, m2 |
| description | TEXT NULL | |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps + soft_deletes | | |

### 10. `service_pricings`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| service_id | BIGINT FK → services(id) | CASCADE delete |
| branch_id | BIGINT FK → branches(id) | CASCADE delete |
| price | DECIMAL(15,2) | |
| min_weight | DECIMAL(8,2) NULL | |
| max_weight | DECIMAL(8,2) NULL | |
| estimated_days | INT | DEFAULT 1 |
| is_active | BOOLEAN | DEFAULT TRUE |
| UNIQUE | (service_id, branch_id) | |
| timestamps + soft_deletes | | |

### 11. `orders`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| order_number | VARCHAR(30) UNIQUE | CAB-20260709-00001 |
| branch_id | BIGINT FK → branches(id) | |
| customer_id | BIGINT FK → customers(id) | NULL (walk-in) |
| customer_name | VARCHAR(100) NULL | For walk-in |
| customer_phone | VARCHAR(20) NULL | |
| created_by | BIGINT FK → users(id) | |
| status | VARCHAR(30) | Stored as VARCHAR — app-level validation via enum classes \| Allowed: draft, pending, processing, completed, cancelled |
| total_amount | DECIMAL(15,2) | |
| discount_amount | DECIMAL(15,2) | DEFAULT 0 |
| point_discount | DECIMAL(15,2) | DEFAULT 0 |
| grand_total | DECIMAL(15,2) | |
| paid_amount | DECIMAL(15,2) | DEFAULT 0 |
| change_amount | DECIMAL(15,2) | DEFAULT 0 |
| payment_status | VARCHAR(30) | DEFAULT 'unpaid' \| Stored as VARCHAR — app-level validation via enum classes \| Allowed: unpaid, paid, refunded, partial_refund |
| payment_method | VARCHAR(20) NULL | Stored as VARCHAR — app-level validation via enum classes \| Allowed: cash, transfer, qris, gateway |
| paid_at | TIMESTAMP NULL | |
| notes | TEXT NULL | |
| qr_token | VARCHAR(64) NULL UNIQUE | QR identifier |
| tracking_token | VARCHAR(64) NULL UNIQUE | UUID |
| finished_at | TIMESTAMP NULL | |
| timestamps + soft_deletes | | |

### 12. `order_items`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| order_id | BIGINT FK → orders(id) | CASCADE delete |
| service_id | BIGINT FK → services(id) | |
| quantity | DECIMAL(10,2) | kg/pcs/m2 |
| price_per_unit | DECIMAL(15,2) | |
| subtotal | DECIMAL(15,2) | |
| qr_token | VARCHAR(64) NULL UNIQUE | UUID per item |
| timestamps | | |

### 13. `production_statuses` (static)

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| code | VARCHAR(20) UNIQUE | TERIMA, PILAH, CUCI, KERING, LIPAT, CEK, SIAP, DIAMBIL |
| name | VARCHAR(50) | Terima, Pilah, Cuci, dll |
| sequence | INT | 1–8 |
| color | VARCHAR(7) | #FF6B00 for active |
| description | TEXT NULL | |

### 14. `order_item_status_logs`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| order_item_id | BIGINT FK → order_items(id) | CASCADE delete |
| production_status_id | BIGINT FK → production_statuses(id) | NULL |
| note | TEXT NULL | |
| scanned_by | BIGINT FK → users(id) | NULL on delete |
| scan_time | TIMESTAMP | DEFAULT NOW() |

### 15. `payments`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| order_id | BIGINT FK → orders(id) | CASCADE delete |
| amount | DECIMAL(15,2) | |
| method | VARCHAR(30) | Stored as VARCHAR — app-level validation via enum classes \| Allowed: cash, transfer, qris, gateway |
| reference | VARCHAR(100) NULL | Transfer ref |
| paid_at | TIMESTAMP | |
| created_by | BIGINT FK → users(id) | |
| notes | TEXT NULL | |
| timestamps | | |

### 16. `refunds`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| order_id | BIGINT FK → orders(id) | CASCADE delete |
| payment_id | BIGINT FK → payments(id) NULL | |
| amount | DECIMAL(15,2) | |
| reason | TEXT | |
| status | VARCHAR(20) | DEFAULT 'requested' \| Stored as VARCHAR — app-level validation via enum classes \| Allowed: requested, approved, completed, rejected |
| requested_by | BIGINT FK → users(id) | Cashier |
| followed_by | BIGINT FK → users(id) NULL | CS |
| approved_by | BIGINT FK → users(id) NULL | Branch Admin |
| approved_at | TIMESTAMP NULL | |
| completed_by | BIGINT FK → users(id) NULL | Cashier |
| timestamps | | |

### 17. `promotions`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| code | VARCHAR(30) UNIQUE | |
| name | VARCHAR(150) | |
| description | TEXT NULL | |
| type | VARCHAR(20) | Stored as VARCHAR — app-level validation via enum classes \| Allowed: percentage, fixed, buy_x_get_y |
| value | DECIMAL(15,2) | |
| min_order_amount | DECIMAL(15,2) | DEFAULT 0 |
| min_order_items | INT | DEFAULT 1 |
| max_discount_amount | DECIMAL(15,2) NULL | |
| applicable_service_ids | JSON NULL | |
| buy_quantity | INT NULL | |
| get_type | VARCHAR(30) NULL | Stored as VARCHAR — app-level validation via enum classes \| Allowed: free, discount_percent, discount_fixed |
| get_value | DECIMAL(15,2) NULL | |
| start_date | DATETIME | |
| end_date | DATETIME | |
| usage_limit_per_customer | INT NULL | |
| total_usage_limit | INT NULL | |
| total_used | INT | DEFAULT 0 |
| is_all_branches | BOOLEAN | DEFAULT TRUE |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps + soft_deletes | | |

### 18. `promotion_branches`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| promotion_id | BIGINT FK → promotions(id) | CASCADE delete |
| branch_id | BIGINT FK → branches(id) | CASCADE delete |
| is_active | BOOLEAN | DEFAULT TRUE |
| UNIQUE | (promotion_id, branch_id) | |
| timestamps | | |

### 19. `promotion_usages`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| promotion_id | BIGINT FK → promotions(id) | CASCADE delete |
| order_id | BIGINT FK → orders(id) | CASCADE delete |
| branch_id | BIGINT FK → branches(id) NULL | |
| customer_id | BIGINT FK → customers(id) NULL | |
| discount_amount | DECIMAL(15,2) | |
| applied_by | BIGINT FK → users(id) NULL | |
| created_at | TIMESTAMP | |

### 20. `chart_of_accounts`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| code | VARCHAR(20) UNIQUE | 1-1000 |
| name | VARCHAR(150) | |
| category | VARCHAR(30) | Stored as VARCHAR — app-level validation via enum classes \| Allowed: asset, liability, equity, revenue, expense |
| normal_balance | VARCHAR(6) NULL | Stored as VARCHAR — app-level validation via enum classes \| Allowed: debit, credit |
| is_tax_account | BOOLEAN | DEFAULT FALSE |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps + soft_deletes | | |

### 21. `journal_entries`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| entry_number | VARCHAR(30) UNIQUE | JRN-20260709-0001 |
| description | TEXT NULL | |
| entry_date | DATE | |
| period_id | BIGINT FK → accounting_periods(id) | |
| branch_id | BIGINT FK → branches(id) | NULL for global |
| type | VARCHAR(20) NULL | Stored as VARCHAR — app-level validation via enum classes \| Allowed: auto, manual, adjustment |
| reference_type | VARCHAR(50) NULL | 'order','expense','tax' |
| reference_id | BIGINT NULL | |
| created_by | BIGINT FK → users(id) | |
| timestamps | | |

### 22. `journal_entry_lines`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| journal_entry_id | BIGINT FK → journal_entries(id) | CASCADE delete |
| account_id | BIGINT FK → chart_of_accounts(id) | |
| debit | DECIMAL(15,2) | DEFAULT 0 |
| credit | DECIMAL(15,2) | DEFAULT 0 |
| description | TEXT NULL | |
| timestamps | | |

### 23. `accounting_periods`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| name | VARCHAR(100) | Juli 2026 |
| start_date | DATE | |
| end_date | DATE | |
| is_closed | BOOLEAN | DEFAULT FALSE |
| closed_at | TIMESTAMP NULL | |
| is_active | BOOLEAN | DEFAULT FALSE |
| timestamps | | |

### 24. `expenses`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| branch_id | BIGINT FK → branches(id) | CASCADE delete |
| created_by | BIGINT FK → users(id) | CASCADE delete |
| category | VARCHAR(50) | |
| amount | DECIMAL(12,2) | |
| is_taxable | BOOLEAN | DEFAULT FALSE |
| description | TEXT NULL | |
| posted_at | DATE | |
| timestamps + soft_deletes | | |

### 25. `tax_configurations`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| regime | VARCHAR(10) | Stored as VARCHAR — app-level validation via enum classes \| Allowed: none, pp23, pkp |
| rate | DECIMAL(5,4) | 0.005 / 0.11 |
| effective_date | DATE NULL | |
| revenue_account_id | BIGINT FK → chart_of_accounts(id) NULL | |
| payable_account_id | BIGINT FK → chart_of_accounts(id) NULL | |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps | | |

### 26. `tax_logs`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| journal_entry_id | BIGINT FK → journal_entries(id) | NULL on delete |
| base_amount | DECIMAL(15,2) | |
| tax_amount | DECIMAL(15,2) | |
| rate | DECIMAL(5,4) | |
| regime | VARCHAR(10) NULL | Stored as VARCHAR — app-level validation via enum classes \| Allowed: pp23, pkp |
| period | VARCHAR(7) NULL | 2026-07 |
| timestamps | | |

### 27. `inventory_items`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| code | VARCHAR(20) UNIQUE | INV-00001 |
| name | VARCHAR(150) | |
| category | VARCHAR(50) NULL | Stored as VARCHAR — app-level validation via enum classes \| Allowed: packaging, chemical, stationery, other |
| description | TEXT NULL | |
| unit | VARCHAR(30) | DEFAULT 'pcs' |
| min_stock | DECIMAL(12,2) | DEFAULT 0 |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps + soft_deletes | | |

### 28. `inventory_batches`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| inventory_item_id | BIGINT FK → inventory_items(id) | CASCADE delete |
| branch_id | BIGINT FK → branches(id) | CASCADE delete |
| batch_code | VARCHAR(50) | BATCH-001 |
| quantity | DECIMAL(12,2) | DEFAULT 0 |
| unit_cost | DECIMAL(15,2) | |
| notes | TEXT NULL | |
| received_at | TIMESTAMP | |
| expired_at | TIMESTAMP NULL | |
| timestamps | | |

### 29. `inventory_transactions`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| inventory_item_id | BIGINT FK → inventory_items(id) | |
| branch_id | BIGINT FK → branches(id) NULL | |
| inventory_batch_id | BIGINT FK → inventory_batches(id) | NULL on delete |
| type | VARCHAR(20) | Stored as VARCHAR — app-level validation via enum classes \| Allowed: purchase, usage, adjustment_plus, adjustment_minus, transfer_out, transfer_in |
| quantity | DECIMAL(12,2) | Always positive |
| unit_cost | DECIMAL(15,2) NULL | |
| before_stock | DECIMAL(12,2) | DEFAULT 0 |
| after_stock | DECIMAL(12,2) | DEFAULT 0 |
| reference | VARCHAR(100) NULL | |
| note | VARCHAR(255) NULL | |
| reference_type | VARCHAR(50) NULL | |
| reference_id | BIGINT NULL | |
| created_by | BIGINT FK → users(id) | |
| timestamps | | |

### 30. `gateway_configurations`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| is_production | BOOLEAN | DEFAULT FALSE |
| client_key | TEXT | |
| server_key | TEXT | |
| merchant_id | VARCHAR(100) NULL | |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps | | |

### 31. `gateway_payments`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| order_id | BIGINT FK → orders(id) | CASCADE delete |
| transaction_id | VARCHAR(100) NULL UNIQUE | From Midtrans |
| gross_amount | DECIMAL(15,2) | |
| status | VARCHAR(30) | DEFAULT 'pending' \| Stored as VARCHAR — app-level validation via enum classes \| Allowed: pending, success, failed, expired, refund |
| payment_type | VARCHAR(30) NULL | bank_transfer, gopay, qris |
| fraud_status | VARCHAR(20) NULL | |
| va_number | VARCHAR(50) NULL | |
| bill_key | VARCHAR(50) NULL | |
| biller_code | VARCHAR(50) NULL | |
| qr_url | TEXT NULL | |
| expiry_time | DATETIME NULL | |
| raw_response | JSON NULL | |
| paid_at | TIMESTAMP NULL | |
| timestamps | | |

### 32. `settings`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| group | VARCHAR(50) | general, tax, loyalty, gateway |
| key | VARCHAR(100) | |
| value | JSON | |
| type | VARCHAR(20) NULL | Stored as VARCHAR — app-level validation via enum classes \| Allowed: string, number, boolean, json |
| is_public | BOOLEAN | DEFAULT FALSE |
| description | TEXT NULL | |
| UNIQUE | (group, key) | |
| timestamps | | |

### 33. `branch_settings`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| branch_id | BIGINT FK → branches(id) | CASCADE delete |
| group | VARCHAR(50) | |
| key | VARCHAR(100) | |
| value | JSON | |
| type | VARCHAR(20) NULL | Stored as VARCHAR — app-level validation via enum classes \| Allowed: string, number, boolean, json |
| UNIQUE | (branch_id, group, key) | |
| timestamps | | |

### 34. `daily_cash_flows`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| branch_id | BIGINT FK → branches(id) | CASCADE delete |
| date | DATE | |
| opening_balance | DECIMAL(15,2) | DEFAULT 0 |
| total_revenue | DECIMAL(15,2) | DEFAULT 0 |
| total_expense | DECIMAL(15,2) | DEFAULT 0 |
| closing_balance | DECIMAL(15,2) | DEFAULT 0 |
| is_reconciled | BOOLEAN | DEFAULT FALSE |
| UNIQUE | (branch_id, date) | |
| timestamps | | |

### 35. `activity_logs`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| user_id | BIGINT FK → users(id) NULL | |
| branch_id | BIGINT FK → branches(id) NULL | |
| loggable_type | VARCHAR(100) | Model class |
| loggable_id | BIGINT | Model ID |
| event | VARCHAR(30) | created, updated, deleted |
| old_values | JSON NULL | |
| new_values | JSON NULL | |
| description | VARCHAR(255) NULL | |
| ip_address | VARCHAR(45) NULL | |
| user_agent | TEXT NULL | |
| timestamps | | |

## Framework Tables

These tables are managed by Laravel itself and not by application migrations.

### 36. `sessions`

| Column | Type | Notes |
|--------|------|-------|
| id | VARCHAR PK | Session ID |
| user_id | BIGINT FK → users(id) NULL | Indexed |
| ip_address | VARCHAR(45) NULL | |
| user_agent | TEXT NULL | |
| payload | LONGTEXT | |
| last_activity | INT | Indexed |

### 37. `cache`

| Column | Type | Notes |
|--------|------|-------|
| key | VARCHAR PK | |
| value | MEDIUMTEXT | |
| expiration | BIGINT | Indexed |

### 38. `cache_locks`

| Column | Type | Notes |
|--------|------|-------|
| key | VARCHAR PK | |
| owner | VARCHAR | |
| expiration | BIGINT | Indexed |

### 39. `jobs`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| queue | VARCHAR | Indexed |
| payload | LONGTEXT | |
| attempts | UNSIGNED TINYINT | |
| reserved_at | UNSIGNED INT NULL | |
| available_at | UNSIGNED INT | |
| created_at | UNSIGNED INT | |

### 40. `job_batches`

| Column | Type | Notes |
|--------|------|-------|
| id | VARCHAR PK | |
| name | VARCHAR | |
| total_jobs | INT | |
| pending_jobs | INT | |
| failed_jobs | INT | |
| failed_job_ids | LONGTEXT | |
| options | MEDIUMTEXT NULL | |
| cancelled_at | INT NULL | |
| created_at | INT | |
| finished_at | INT NULL | |

### 41. `failed_jobs`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| uuid | VARCHAR UNIQUE | |
| connection | TEXT | |
| queue | TEXT | |
| payload | LONGTEXT | |
| exception | LONGTEXT | |
| failed_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |

### 42. `personal_access_tokens`

Laravel Sanctum table. Used for API authentication.

## Indexes

- `orders`: branch_id, customer_id, status, payment_status, tracking_token (UNIQUE), qr_token (UNIQUE)
- `order_items`: order_id, qr_token (UNIQUE)
- `order_item_status_logs`: order_item_id, production_status_id
- `inventory_batches`: (inventory_item_id, branch_id), received_at
- `inventory_transactions`: inventory_item_id, branch_id, type
- `activity_logs`: (loggable_type, loggable_id), event
- `journal_entries`: entry_date, period_id, branch_id
- `settings`: (group, key) UNIQUE
- `branch_settings`: (branch_id, group, key) UNIQUE
- `promotion_branches`: (promotion_id, branch_id) UNIQUE
- `daily_cash_flows`: (branch_id, date) UNIQUE
- All foreign keys indexed automatically by Laravel
