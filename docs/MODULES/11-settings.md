# Module 11: Settings

## Overview

Global and per-branch key-value settings with 9 groups. Owner/Super Admin manage global, Branch Admin manage their own overrides.

## Tables

- `settings` — Global key-value store
- `branch_settings` — Per-branch overrides (FK to branches)

## 9 Setting Groups

| # | Group | Key Examples | Managed By |
|---|-------|-------------|-----------|
| 1 | General | `store_name`, `timezone`, `currency`, `logo` | Dev, SA |
| 2 | Branch Config | `opening_time`, `closing_time`, `daily_capacity` | BA |
| 3 | Tax | `regime`, `pp23_rate`, `ppn_rate`, `accts` | Dev, Owner, SA |
| 4 | Loyalty | `points_ratio`, `redeem_rate`, `expiry_days`, `auto_upgrade` | Dev, Owner, SA |
| 5 | Gateway | `is_active`, `is_production`, `client_key`, `server_key` | Dev |
| 6 | Accounting | `active_period_id`, `default_revenue_acct`, `default_expense_acct` | Dev, SA |
| 7 | Order | `prefix_pattern`, `auto_confirm`, `default_status` | Dev, SA |
| 8 | Notification | WA templates (see FEATURES/wa-templates.md) | Dev, SA |
| 9 | Inventory | `default_min_stock`, `enable_fifo` | Dev, SA, BA |

## Helper Functions

```php
// Global setting
function setting(string $key, mixed $default = null): mixed
{
    $s = \App\Models\Setting::where('key', $key)->first();
    return $s ? json_decode($s->value, true) : $default;
}

// Branch-specific, fallback to global
function branchSetting(string $key, mixed $default = null, ?int $branchId = null): mixed
{
    $branchId = $branchId ?? currentBranchId();
    $bs = \App\Models\BranchSetting::where(['branch_id' => $branchId, 'key' => $key])->first();
    if ($bs) return json_decode($bs->value, true);
    return setting($key, $default);
}

// Usage in code
$ratio = branchSetting('loyalty.points_ratio', 1000);
```

## Default Settings (Seeder)

```php
[
    'general.store_name' => 'Istana Laundry',
    'general.timezone' => 'Asia/Jakarta',
    'general.currency' => 'IDR',
    'tax.regime' => 'none',
    'tax.pp23_rate' => 0.5,
    'tax.ppn_rate' => 11,
    'loyalty.points_ratio' => 1000,
    'loyalty.points_redeem_rate' => 100,
    'loyalty.points_expiry_days' => 90,
    'loyalty.min_order_amount' => 0,
    'loyalty.auto_upgrade' => true,
    'gateway.is_active' => false,
    'gateway.is_production' => false,
    'accounting.active_period_id' => null,
    'order.prefix_pattern' => '{BRANCH_CODE}-{YYYYMMDD}-{XXXXX}',
    'inventory.default_min_stock' => 5,
    'inventory.enable_fifo' => true,
]
```

## UI

```
SETTINGS → Menu Grid
┌──────────────────────────────────────────────┐
│  Settings                                     │
│                                              │
│  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐│
│  │ General │ │ Branch │ │  Tax   │ │Loyalty ││
│  └────────┘ └────────┘ └────────┘ └────────┘│
│  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐│
│  │Gateway │ │Account │ │ Order  │ │  Notif  ││
│  └────────┘ └────────┘ └────────┘ └────────┘│
│  ┌────────┐                                  │
│  │Inven.. │                                  │
│  └────────┘                                  │
└──────────────────────────────────────────────┘

SETTINGS → Tax (example)
┌─────────────────────────────────────────────┐
│  Konfigurasi Pajak                          │
│                                             │
│  Regime: ◉ Non-PKP  ○ PP 23  ○ PKP          │
│                                             │
│  ── PP 23 Settings (if selected) ──         │
│  Tarif       : [0,5] %                      │
│                                             │
│  ── PKP Settings (if selected) ──           │
│  Tarif PPN   : [11] %                       │
│  Auto Jurnal : [✔ Ya]                       │
│                                             │
│  [Simpan]                                   │
└─────────────────────────────────────────────┘
```

## Files

```
app/Models/Setting.php
app/Models/BranchSetting.php
app/Services/SettingService.php
app/Helpers/settings.php
app/Http/Controllers/Web/SettingsController.php
app/Http/Controllers/Web/BranchSettingController.php
app/Http/Controllers/Web/GatewayConfigurationController.php
database/migrations/create_settings_table.php
database/migrations/create_branch_settings_table.php
database/seeders/DefaultSettingsSeeder.php
resources/views/settings/index.blade.php          (menu grid)
resources/views/settings/general.blade.php
resources/views/settings/tax.blade.php
resources/views/settings/loyalty.blade.php
resources/views/settings/gateway.blade.php
resources/views/settings/accounting.blade.php
resources/views/settings/order.blade.php
resources/views/settings/notification.blade.php
resources/views/settings/inventory.blade.php
resources/views/settings/partials/nav.blade.php
resources/views/settings/branch-settings.blade.php
resources/views/settings/partials/group-layout.blade.php
resources/views/settings/activity-logs.blade.php
resources/views/settings/backup.blade.php
```

## Routes

All routes use the `admin.*` name prefix, nested under `auth` → `verified` → `branch` middleware.

**Settings:**
| Action | Name | Permission |
|--------|------|-----------|
| Settings index (menu grid) | `admin.settings.index` | `settings.read\|settings.update\|edit_global_settings` |
| Settings group page | `admin.settings.group` | `settings.read` |
| Settings group update | `admin.settings.group.update` | `settings.update` |

**Branch Settings:**
| Action | Name | Permission |
|--------|------|-----------|
| Branch settings page | `admin.branch-settings.index` | `edit_branch_settings` |
| Branch settings update | `admin.branch-settings.update` | `edit_branch_settings` |

**Gateway Configuration:**
| Action | Name | Permission |
|--------|------|-----------|
| Gateway config page | `admin.settings.gateway` | `manage_gateway_config` |
| Gateway config update | `admin.settings.gateway.update` | `manage_gateway_config` |
