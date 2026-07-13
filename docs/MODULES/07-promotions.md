# Module 07: Promotions & Marketing

## Overview

Promotions engine supporting percentage, fixed, and buy-get discount types with per-branch toggle.

## Tables

- `promotions` — Promotion definitions
- `promotion_branches` — Branch eligibility (if not all branches)
- `promotion_usages` — Usage tracking

## Promotion Types

| Type | Value | Max Discount | Example |
|------|-------|-------------|---------|
| `percentage` | % (e.g. 10) | Yes (optional) | 10% off max Rp 5.000 |
| `fixed` | Nominal (e.g. Rp 10.000) | N/A | Rp 10.000 off |
| `buy_x_get_y` | See below | N/A | Buy 3kg, get 1kg free |

### Buy Get Config
```
buy_quantity: 3 (X)
get_type: 'free' | 'discount_percent' | 'discount_fixed'
get_value: 100 (free) | 50 (50% off) | 5000 (Rp 5.000 off)
```

## Conditions

| Condition | Description |
|-----------|-------------|
| `min_order_amount` | Minimum transaction total |
| `min_order_items` | Minimum number of items |
| `max_discount_amount` | Maximum discount (for percentage type) |
| `applicable_service_ids` | Restrict to specific services |
| `usage_limit_per_customer` | Max uses per customer |
| `total_usage_limit` | Max total usages across all customers |
| `start_date` / `end_date` | Active period |

## Scope

- `is_all_branches = true` → Available at all branches
- `is_all_branches = false` → Only at selected branches in `promotion_branches`
- Branch Admin can **disable** promotions at their branch via `branch_settings` (promotion_override)

## Usage in POS

```
1. CS enters order items
2. System shows eligible promotions (auto-filtered by conditions)
3. CS selects promotion → discount calculated
4. Customer can also redeem points (can combine with promotion)
5. Only 1 promotion + points per order

Rule: Promo applied first, then points on remaining amount
```

## Finance Impact

```
Dr. Beban Promosi (from discount)
    Cr. Pendapatan (reducing revenue)
```

Auto-journal entry created when order is paid.

## UI

```
PROMOTIONS → Index
┌──────────────────────────────────────────────┐
│  Promotions                   [+ Tambah Promo]│
├──────────────────────────────────────────────┤
│ Code │ Name       │ Type   │ Value │ Periode │
├──────┼────────────┼────────┼───────┼─────────┤
│ LBR26│ Lebaran 26 │ %      │ 10%   │ 1-15 Jul│
│ FRSHP│ Free Ship  │ Buy X  │ Free  │ 01-31 Jul│
└──────┴────────────┴────────┴───────┴─────────┘

CREATE PROMO
┌──────────────────────────────────────────────┐
│  Nama       : [________________________]      │
│  Kode       : [______________] Auto?          │
│  Tipe       : [Percentage ▼]                  │
│  Value      : [10] %                          │
│  Max Diskon : [5.000]                         │
│                                               │
│  Syarat:                                      │
│  Min Order  : [50.000]                        │
│  Min Item   : [1]                             │
│  Layanan    : [✔ Semua] [Pilih...]            │
│                                               │
│  Periode:                                     │
│  Mulai  : [01/07/2026 00:00]                  │
│  Selesai: [15/07/2026 23:59]                  │
│                                               │
│  Batas Pakai:                                 │
│  Per Cust : [3]                               │
│  Total    : [100]                             │
│                                               │
│  Branch: ◉ Semua  ○ Pilih                     │
│                                               │
│  Status: [✔ Aktif]                            │
│                                               │
│  [Simpan]    [Batal]                          │
└──────────────────────────────────────────────┘
```

## Files

```
app/Models/Promotion.php
app/Models/PromotionBranch.php
app/Models/PromotionUsage.php
app/Enums/PromotionType.php
app/Services/PromotionService.php
app/Services/DiscountCalculator.php
app/Http/Controllers/Web/PromotionController.php
database/migrations/create_promotions_table.php
database/migrations/create_promotion_branches_table.php
database/migrations/create_promotion_usages_table.php
resources/views/promotions/index.blade.php
resources/views/promotions/show.blade.php
resources/views/promotions/create.blade.php
resources/views/promotions/edit.blade.php
```
