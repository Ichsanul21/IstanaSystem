# Module 02: Branch & Workshop Management

## Overview

Multi-branch management with central workshops. Each branch belongs to a workshop. Branch-specific pricing, promotions, and settings.

## Tables

- `workshops` — Central workshop locations
- `branches` — Branch stores, FK to `workshops.id`

## Features

### Workshop Management
- Every branch is associated with a workshop (`workshop_id`)
- Workshop Admin sees orders from all branches assigned to their workshop
- Multiple branches can belong to one workshop

### Branch CRUD
- Developer & Super Admin manage branches
- Fields: code, name, workshop, address, phone, opening hours, daily capacity
- Auto-generated branch code: `CAB-001`, `CAB-002`

### Branch Context
- `SetBranchContext` middleware sets `session('branch_id')` on every request
- Branch switcher available for Developer/Owner/Super Admin
- Branch Admin, Cashier, CS are bound to their assigned branch
- `HasBranchScope` trait auto-filters queries:
  ```php
  class Order extends Model {
      use HasBranchScope;
  }
  // Order::all() → WHERE branch_id = current
  ```

### Branch-Specific Config
- `branch_settings` table for key-value overrides
- Each branch can have different: pricing, promo toggles, loyalty rate, operating hours
- Branch Admin can modify branch-specific settings

### Branch Switcher UI

```
HEADER → Branch Switcher
┌──────────────────────┐
│  [🏢 Cabang A    ▼]  │
├──────────────────────┤
│  ○ Cabang A          │
│  ○ Cabang B          │ ← click switches context
│  ○ Cabang C          │
└──────────────────────┘
```

### Daily Cash Flow

- `daily_cash_flows` table per branch per date
- Created automatically at first transaction of the day
- `opening_balance` set by Branch Admin each morning
- `closing_balance` calculated from revenue - expenses

## Files

```
app/Models/Branch.php
app/Models/Workshop.php
app/Models/DailyCashFlow.php
app/Traits/HasBranchScope.php
app/Http/Middleware/SetBranchContext.php
app/Http/Controllers/Web/BranchController.php
app/Http/Controllers/Web/WorkshopController.php
database/migrations/create_workshops_table.php
database/migrations/create_branches_table.php
database/migrations/create_daily_cash_flows_table.php
resources/views/branches/index.blade.php
resources/views/branches/create.blade.php
resources/views/branches/edit.blade.php
resources/views/workshops/index.blade.php
```
