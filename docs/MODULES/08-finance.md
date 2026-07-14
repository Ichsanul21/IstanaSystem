# Module 08: Finance

## Overview

Double-entry accounting system with auto-posting for POS payments, expenses, discounts, and tax. Custom accounting periods, COA management, cost distribution.

## Tables

- `chart_of_accounts` — COA tree
- `journal_entries` — Journal header
- `journal_entry_lines` — Debit/credit lines
- `accounting_periods` — Custom periods (not locked to calendar)
- `expenses` — Expense records
- `tax_configurations` — Tax regime settings
- `tax_logs` — Tax calculation logs
- `daily_cash_flows` — Per-branch daily cash summary

## Chart of Accounts

| Code | Name | Category | Normal Balance |
|------|------|----------|---------------|
| 1-1000 | Kas | Asset | Debit |
| 1-1100 | Bank BCA | Asset | Debit |
| 1-2000 | Piutang Usaha | Asset | Debit |
| 1-3000 | Inventory Asset | Asset | Debit |
| 1-4000 | Inventory In Transit | Asset | Debit |
| 2-1000 | Hutang Usaha | Liability | Credit |
| 2-2000 | Hutang Pajak | Liability | Credit |
| 3-1000 | Modal | Equity | Credit |
| 3-2000 | Laba Ditahan | Equity | Credit |
| 4-1000 | Pendapatan Laundry | Revenue | Credit |
| 4-2000 | Pendapatan Lain | Revenue | Credit |
| 5-1000 | Beban Gaji | Expense | Debit |
| 5-2000 | Beban Sewa | Expense | Debit |
| 5-3000 | Beban Operasional Inventory | Expense | Debit |
| 5-4000 | Beban Promosi | Expense | Debit |
| 5-5000 | Beban Pajak | Expense | Debit |
| 5-6000 | Beban Listrik & Air | Expense | Debit |
| 5-7000 | Beban Lain-lain | Expense | Debit |

## Auto-Journaling Rules

### POS Payment

```
When order is paid (cash):
  Dr. Kas                     Rp XXX
    Cr. Pendapatan Laundry        Rp XXX

When order is paid (gateway):
  Dr. Bank BCA                Rp XXX
    Cr. Pendapatan Laundry        Rp XXX
```

### Discount (Promosi)

```
  Dr. Beban Promosi           Rp XXX
    Cr. Pendapatan Laundry        Rp XXX
```

### Expense

```
When expense is recorded:
  Dr. Beban [category]        Rp XXX
    Cr. Kas                       Rp XXX
```

### Inventory Usage (COGS)

```
When stock is used (FIFO):
  Dr. Beban Operasional Inventory   Rp XXX
    Cr. Inventory Asset                 Rp XXX
```

## Tax Regimes

### PP 23/2018 (0.5% of revenue)
```
Auto-calculate monthly:
  Tax base = total revenue for the month
  Tax = base × 0.5%
  
  Dr. Beban Pajak (PP 23)            Rp XXX
    Cr. Hutang Pajak (PP 23)             Rp XXX
```

### PKP (PPN 11%)
```
Per transaction:
  PPN = (grand_total / 1.11) × 0.11
  
  Dr. Piutang Usaha / Kas           Rp XXX
    Cr. Pendapatan Laundry              Rp XXX
    Cr. Hutang PPN Keluaran             Rp XXX
```

### No Tax (`none`)
- No automatic tax calculation or journaling

### Toggle in Settings
- Owner/Super Admin chooses: `none`, PP 23, or PKP
- System auto-calculates and auto-journals

## Accounting Periods

- Custom periods (e.g., Juli 2026, not locked to calendar month)
- Only one period can be `is_active = true` at a time
- Created by Developer / Super Admin
- When a period is closed (`is_closed = true`), no new entries can be posted to it

## Cost Distribution

For multi-branch operations with central workshop:

```
Workshop cost (detergen, listrik, gaji staff workshop)
  → Distributed to branches by order volume ratio
  
Example:
  Workshop cost: Rp 10.000.000
  Branch A orders: 60%
  Branch B orders: 40%
  → Branch A bears Rp 6.000.000
  → Branch B bears Rp 4.000.000
  
  Dr. Beban [Branch A]            Rp 6.000.000
  Dr. Beban [Branch B]            Rp 4.000.000
    Cr. Beban Terdistribusi            Rp 10.000.000
```

## Finance Dashboard

```
FINANCE → Dashboard
┌──────────────────────────────────────────────┐
│  Periode: Juli 2026     [Ganti ▼]            │
├──────────────────────────────────────────────┤
│  ┌──────┬──────┬──────┬───────┬──────┐       │
│  │Revenue│Biaya │Laba  │Piutang│Kas   │       │
│  │45 Jt  │28 Jt │17 Jt │ 2 Jt  │15 Jt │       │
│  └──────┴──────┴──────┴───────┴──────┘       │
│                                               │
│  Tabs: [Jurnal] [COA] [Buku Besar] [Pajak]    │
│                                               │
│  ─── JURNAL ───                                │
│  Tgl     │ No Jurnal │ Deskripsi    │ Debit  │
│  09 Jul  │ JRN-0001  │ Order B001   │ 44.400 │
│  09 Jul  │ JRN-0002  │ Beban Listrik│ 500.000│
└──────────────────────────────────────────────┘
```

## Routes

All finance routes use the `admin.finance.*` name prefix, nested under `auth` → `verified` → `branch` middleware, with the group middleware `permission:finance.read|create_manual_journal|manage_accounting_periods|manage_expenses`.

**Dashboard & Overview:**
| Action | Name | Permission |
|--------|------|-----------|
| Finance dashboard | `admin.finance.index` | `finance.read\|create_manual_journal\|manage_accounting_periods\|manage_expenses` |
| COA overview | `admin.finance.accounts` | (same) |
| Journal list | `admin.finance.journal` | (same) |
| Create journal form | `admin.finance.journal.create` | (same) |
| Store journal | `admin.finance.journal.store` | (same) |
| Trial balance | `admin.finance.trial-balance` | (same) |
| Income statement | `admin.finance.income-statement` | (same) |

**Chart of Accounts (CRUD):**
| Action | Name |
|--------|------|
| Create form | `admin.finance.coa.create` |
| Store | `admin.finance.coa.store` |
| Edit form | `admin.finance.coa.edit` |
| Update | `admin.finance.coa.update` |
| Delete | `admin.finance.coa.destroy` |

**Accounting Periods (CRUD):**
| Action | Name |
|--------|------|
| List | `admin.finance.periods.index` |
| Create form | `admin.finance.periods.create` |
| Store | `admin.finance.periods.store` |
| Edit form | `admin.finance.periods.edit` |
| Update | `admin.finance.periods.update` |
| Delete | `admin.finance.periods.destroy` |
| Close period | `admin.finance.periods.close` |

**Expenses (CRUD):**
| Action | Name |
|--------|------|
| List | `admin.finance.expenses.index` |
| Create form | `admin.finance.expenses.create` |
| Store | `admin.finance.expenses.store` |
| Edit form | `admin.finance.expenses.edit` |
| Update | `admin.finance.expenses.update` |
| Delete | `admin.finance.expenses.destroy` |

## Files

```
app/Models/ChartOfAccount.php
app/Models/JournalEntry.php
app/Models/JournalEntryLine.php
app/Models/AccountingPeriod.php
app/Models/Expense.php
app/Models/TaxConfiguration.php
app/Models/TaxLog.php
app/Services/Finance/JournalService.php
app/Services/Finance/TaxService.php
app/Services/Finance/CostDistributionService.php
app/Http/Controllers/Web/FinanceController.php
app/Http/Controllers/Web/ChartOfAccountController.php
app/Http/Controllers/Web/AccountingPeriodController.php
app/Http/Controllers/Web/ExpenseController.php
database/migrations/create_chart_of_accounts_table.php
database/migrations/create_journal_entries_table.php
database/migrations/create_journal_entry_lines_table.php
database/migrations/create_accounting_periods_table.php
database/migrations/create_expenses_table.php
database/migrations/create_tax_configurations_table.php
database/migrations/create_tax_logs_table.php
database/seeders/ChartOfAccountSeeder.php
resources/views/finance/index.blade.php
resources/views/finance/accounts.blade.php
resources/views/finance/dashboard.blade.php
resources/views/finance/journal/index.blade.php
resources/views/finance/journal/create.blade.php
resources/views/finance/reports/trial-balance.blade.php
resources/views/finance/reports/income-statement.blade.php
resources/views/finance/coa/index.blade.php
resources/views/finance/coa/create.blade.php
resources/views/finance/coa/edit.blade.php
resources/views/finance/periods/index.blade.php
resources/views/finance/periods/create.blade.php
resources/views/finance/periods/edit.blade.php
resources/views/finance/expenses/index.blade.php
resources/views/finance/expenses/create.blade.php
resources/views/finance/expenses/edit.blade.php
```
