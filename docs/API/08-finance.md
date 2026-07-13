# API: Finance

## GET /api/v1/finance/journal

List journal entries. **Query:** `?period_id=&date_from=&date_to=&account_id=&branch_id=`

## GET /api/v1/finance/journal/{id}

Single journal entry with lines.

## POST /api/v1/finance/journal

Create manual journal entry.

**Request:**
```json
{
    "description": "Koreksi beban listrik",
    "entry_date": "2026-07-09",
    "lines": [
        { "account_id": 6, "debit": 500000, "credit": 0 },
        { "account_id": 1, "debit": 0, "credit": 500000 }
    ]
}
```

## GET /api/v1/finance/coa

List chart of accounts.

## GET /api/v1/finance/coa/{id}

Single account with balance.

## GET /api/v1/finance/coa/{id}/ledger

General ledger for an account. **Query:** `?date_from=&date_to=&period_id=`

## GET /api/v1/finance/trial-balance

Trial balance for a period. **Query:** `?period_id=&branch_id=`

## GET /api/v1/finance/profit-loss

Profit & Loss statement.

## GET /api/v1/finance/balance-sheet

Balance sheet.

## GET /api/v1/finance/expenses

List expenses. **Query:** `?category=&date_from=&date_to=&branch_id=`

## POST /api/v1/finance/expenses

Create expense.

## GET /api/v1/finance/tax/summary

Tax summary for a period. **Query:** `?period=2026-07&regime=pp23`

## GET /api/v1/finance/periods

List accounting periods.

## POST /api/v1/finance/periods

Create accounting period.

## POST /api/v1/finance/periods/{id}/close

Close accounting period.
