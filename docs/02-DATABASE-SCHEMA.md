# Database Schema

## Overview

**Total Tables:** 30+
**Engine:** InnoDB (MySQL) / PostgreSQL
**Charset:** utf8mb4 (MySQL) / UTF-8 (PostgreSQL)

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
| name | VARCHAR(100) | |
| phone | VARCHAR(20) NULL | |
| email | VARCHAR(100) NULL | |
| address | TEXT NULL | |
| id_card_number | VARCHAR(30) NULL | KTP |
| birth_date | DATE NULL | |
| gender | ENUM('L','P') NULL | |
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
| points | INT | + earn / - redeem |
| balance_after | INT | |
| type | ENUM('earn','redeem','expire','adjust') | |
| description | VARCHAR(255) | |
| expiry_date | DATE NULL | |
| expired_at | TIMESTAMP NULL | |
| created_by | BIGINT FK → users(id) | NULL |
| timestamps | | |

### 9. `services`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| code | VARCHAR(10) UNIQUE | CK, CB, ST, EXP, SL, LP, KP, SF |
| name | VARCHAR(100) | Cuci Kering, dll |
| unit | ENUM('kg','pcs','m2') | |
| description | TEXT NULL | |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps + soft_deletes | | |

### 10. `service_pricings`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| service_id | BIGINT FK → services(id) | |
| branch_id | BIGINT FK → branches(id) | |
| price | DECIMAL(15,2) | |
| min_weight | DECIMAL(5,2) | DEFAULT 0 |
| is_active | BOOLEAN | DEFAULT TRUE |
| UNIQUE | (service_id, branch_id) | |
| timestamps | | |

### 11. `orders`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| order_number | VARCHAR(30) UNIQUE | CAB-20260709-00001 |
| branch_id | BIGINT FK → branches(id) | |
| customer_id | BIGINT FK → customers(id) | NULL (walk-in) |
| customer_name | VARCHAR(100) | For walk-in |
| customer_phone | VARCHAR(20) NULL | |
| total_amount | DECIMAL(15,2) | |
| discount_amount | DECIMAL(15,2) | DEFAULT 0 |
| point_discount | DECIMAL(15,2) | DEFAULT 0 |
| grand_total | DECIMAL(15,2) | |
| paid_amount | DECIMAL(15,2) | |
| change_amount | DECIMAL(15,2) | |
| status | ENUM('pending','process','finished','delivered','cancelled') | |
| payment_status | ENUM('unpaid','paid','refunded') | |
| payment_method | ENUM('cash','transfer','qris','gateway') | |
| notes | TEXT NULL | |
| tracking_token | VARCHAR(64) UNIQUE | UUID |
| created_by | BIGINT FK → users(id) | |
| paid_at | TIMESTAMP NULL | |
| finished_at | TIMESTAMP NULL | |
| timestamps + soft_deletes | | |

### 12. `order_items`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| order_id | BIGINT FK → orders(id) | |
| service_id | BIGINT FK → services(id) | |
| quantity | DECIMAL(10,2) | kg/pcs/m2 |
| price_per_unit | DECIMAL(15,2) | |
| subtotal | DECIMAL(15,2) | |
| qr_token | VARCHAR(64) UNIQUE | UUID per item |
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
| order_item_id | BIGINT FK → order_items(id) | |
| production_status_id | BIGINT FK → production_statuses(id) | |
| note | TEXT NULL | |
| scanned_by | BIGINT FK → users(id) | |
| scan_time | TIMESTAMP | DEFAULT NOW() |

### 15. `payments`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| order_id | BIGINT FK → orders(id) | |
| amount | DECIMAL(15,2) | |
| method | ENUM('cash','transfer','qris','gateway') | |
| reference | VARCHAR(100) NULL | Transfer ref |
| paid_at | TIMESTAMP | |
| created_by | BIGINT FK → users(id) | |
| timestamps | | |

### 16. `refunds`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| order_id | BIGINT FK → orders(id) | |
| amount | DECIMAL(15,2) | |
| reason | TEXT | |
| status | ENUM('requested','approved','completed','rejected') | |
| requested_by | BIGINT FK → users(id) | Cashier |
| followed_by | BIGINT FK → users(id) NULL | CS |
| approved_by | BIGINT FK → users(id) NULL | Branch Admin |
| completed_by | BIGINT FK → users(id) NULL | Cashier |
| timestamps | | |

### 17. `promotions`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| code | VARCHAR(30) UNIQUE | |
| name | VARCHAR(100) | |
| description | TEXT NULL | |
| type | ENUM('percentage','fixed','buy_x_get_y') | |
| value | DECIMAL(15,2) | |
| min_order_amount | DECIMAL(15,2) | DEFAULT 0 |
| min_order_items | INT | DEFAULT 1 |
| max_discount_amount | DECIMAL(15,2) NULL | |
| applicable_service_ids | JSON NULL | |
| buy_quantity | INT NULL | |
| get_type | ENUM('free','discount_percent','discount_fixed') NULL | |
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
| promotion_id | BIGINT FK → promotions(id) | |
| branch_id | BIGINT FK → branches(id) | |
| UNIQUE | (promotion_id, branch_id) | |

### 19. `promotion_usages`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| promotion_id | BIGINT FK → promotions(id) | |
| order_id | BIGINT FK → orders(id) | |
| customer_id | BIGINT FK → customers(id) NULL | |
| branch_id | BIGINT FK → branches(id) | |
| discount_amount | DECIMAL(15,2) | |
| applied_by | BIGINT FK → users(id) | |
| created_at | TIMESTAMP | |

### 20. `chart_of_accounts`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| code | VARCHAR(15) | 1-1000 |
| name | VARCHAR(100) | |
| category | ENUM('asset','liability','equity','revenue','expense') | |
| normal_balance | ENUM('debit','credit') | |
| is_tax_account | BOOLEAN | DEFAULT FALSE |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps | | |

### 21. `journal_entries`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| entry_number | VARCHAR(30) UNIQUE | JRN-20260709-0001 |
| description | VARCHAR(255) | |
| entry_date | DATE | |
| period_id | BIGINT FK → accounting_periods(id) | |
| branch_id | BIGINT FK → branches(id) | NULL for global |
| type | ENUM('auto','manual','adjustment') | |
| reference_type | VARCHAR(50) NULL | 'order','expense','tax' |
| reference_id | BIGINT NULL | |
| created_by | BIGINT FK → users(id) | |
| timestamps | | |

### 22. `journal_entry_lines`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| journal_entry_id | BIGINT FK → journal_entries(id) | |
| account_id | BIGINT FK → chart_of_accounts(id) | |
| debit | DECIMAL(15,2) | DEFAULT 0 |
| credit | DECIMAL(15,2) | DEFAULT 0 |
| description | VARCHAR(255) NULL | |

### 23. `accounting_periods`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| name | VARCHAR(100) | Juli 2026 |
| start_date | DATE | |
| end_date | DATE | |
| is_closed | BOOLEAN | DEFAULT FALSE |
| is_active | BOOLEAN | DEFAULT FALSE |
| timestamps | | |

### 24. `expenses`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| branch_id | BIGINT FK → branches(id) | |
| category | VARCHAR(50) | |
| amount | DECIMAL(15,2) | |
| description | TEXT | |
| is_taxable | BOOLEAN | DEFAULT FALSE |
| posted_at | DATE | |
| created_by | BIGINT FK → users(id) | |
| timestamps | | |

### 25. `tax_configurations`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| regime | ENUM('none','pp23','pkp') | |
| rate | DECIMAL(5,2) | 0.5 / 11 |
| revenue_account_id | BIGINT FK → chart_of_accounts(id) | |
| payable_account_id | BIGINT FK → chart_of_accounts(id) | |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps | | |

### 26. `tax_logs`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| journal_entry_id | BIGINT FK → journal_entries(id) | |
| regime | ENUM('pp23','pkp') | |
| base_amount | DECIMAL(15,2) | |
| tax_amount | DECIMAL(15,2) | |
| rate | DECIMAL(5,2) | |
| period | VARCHAR(7) | 2026-07 |
| timestamps | | |

### 27. `inventory_items`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| code | VARCHAR(30) UNIQUE | INV-00001 |
| name | VARCHAR(150) | |
| category | ENUM('packaging','chemical','stationery','other') | |
| unit | VARCHAR(20) | pcs, liter, kg, roll, pack |
| min_stock | INT | DEFAULT 5 |
| is_active | BOOLEAN | DEFAULT TRUE |
| timestamps + soft_deletes | | |

### 28. `inventory_batches`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| inventory_item_id | BIGINT FK → inventory_items(id) | |
| branch_id | BIGINT FK → branches(id) | |
| batch_code | VARCHAR(50) | BATCH-001 |
| quantity | INT | DEFAULT 0 |
| unit_cost | DECIMAL(15,2) | |
| received_at | DATE | |
| expired_at | DATE NULL | |
| notes | TEXT NULL | |
| timestamps + soft_deletes | | |

### 29. `inventory_transactions`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| inventory_item_id | BIGINT FK → inventory_items(id) | |
| branch_id | BIGINT FK → branches(id) | |
| inventory_batch_id | BIGINT FK → inventory_batches(id) NULL | |
| type | ENUM('purchase','usage','adjustment_plus','adjustment_minus','transfer_out','transfer_in') | |
| quantity | INT | Always positive |
| unit_cost | DECIMAL(15,2) | |
| before_stock | INT | |
| after_stock | INT | |
| reference_type | VARCHAR(50) NULL | |
| reference_id | BIGINT NULL | |
| note | VARCHAR(255) NULL | |
| created_by | BIGINT FK → users(id) | |
| created_at | TIMESTAMP | |

### 30. `gateway_configurations`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| is_production | BOOLEAN | DEFAULT FALSE |
| client_key | VARCHAR(255) NULL | |
| server_key | VARCHAR(255) NULL | |
| merchant_id | VARCHAR(100) NULL | |
| is_active | BOOLEAN | DEFAULT FALSE |
| timestamps | | |

### 31. `gateway_payments`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| order_id | BIGINT FK → orders(id) | |
| transaction_id | VARCHAR(100) NULL | From Midtrans |
| gross_amount | DECIMAL(15,2) | |
| status | ENUM('pending','success','failed','expired','refund') | |
| payment_type | VARCHAR(50) NULL | bank_transfer, gopay, qris |
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
| key | VARCHAR(100) UNIQUE | |
| value | TEXT | JSON encoded |
| type | ENUM('string','number','boolean','json') | |
| description | TEXT NULL | |
| timestamps | | |

### 33. `branch_settings`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| branch_id | BIGINT FK → branches(id) | |
| group | VARCHAR(50) | |
| key | VARCHAR(100) | |
| value | TEXT | JSON encoded |
| type | ENUM('string','number','boolean','json') | |
| UNIQUE | (branch_id, key) | |
| timestamps | | |

### 34. `daily_cash_flows`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| branch_id | BIGINT FK → branches(id) | |
| date | DATE | |
| opening_balance | DECIMAL(15,2) | |
| total_revenue | DECIMAL(15,2) | DEFAULT 0 |
| total_expense | DECIMAL(15,2) | DEFAULT 0 |
| closing_balance | DECIMAL(15,2) | |
| is_reconciled | BOOLEAN | DEFAULT FALSE |
| timestamps | | |

### 35. `activity_logs`

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK AI | |
| user_id | BIGINT FK → users(id) NULL | |
| branch_id | BIGINT FK → branches(id) NULL | |
| loggable_type | VARCHAR(100) | Model class |
| loggable_id | BIGINT NULL | Model ID |
| event | VARCHAR(50) | created, updated, deleted |
| old_values | JSON NULL | |
| new_values | JSON NULL | |
| description | VARCHAR(255) | |
| ip_address | VARCHAR(45) NULL | |
| user_agent | VARCHAR(255) NULL | |
| created_at | TIMESTAMP | |

## Indexes

- `orders`: branch_id, customer_id, status, payment_status, tracking_token
- `order_items`: order_id, qr_token (UNIQUE)
- `order_item_status_logs`: order_item_id, production_status_id
- `inventory_batches`: (item_id, branch_id), received_at
- `inventory_transactions`: item_id, branch_id, type
- `activity_logs`: loggable_type, loggable_id, created_at
- `journal_entries`: entry_date, period_id, branch_id
- All foreign keys indexed automatically by Laravel
