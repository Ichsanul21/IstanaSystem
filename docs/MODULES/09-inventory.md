# Module 09: Inventory (FIFO)

## Overview

Full FIFO inventory management with batch tracking, purchase costing, auto-COGS journal, and per-branch stock.

## Tables

- `inventory_items` — Item definitions (code, name, category, unit, min_stock)
- `inventory_batches` — Batch tracking (item_id, branch_id, batch_code, quantity, unit_cost, received_at, expired_at)
- `inventory_transactions` — Stock movement log (batch_id, type, quantity, unit_cost, user_id)

## Categories

| Code | Name | Examples |
|------|------|----------|
| `packaging` | Kemasan | Plastic bags, hanger, tag, paper bag |
| `chemical` | Kimia | Deterjen, pewangi, pemutih, softener |
| `stationery` | ATK | Kertas struk, tinta printer, stapler |
| `consumable` | Habis Pakai | Masker, sarung tangan, kain lap |
| `other` | Lainnya | Seragam, dll |

## FIFO Engine

### Purchase (Stock In)
- Create new batch with quantity + unit_cost
- Journal: Dr. Inventory Asset, Cr. Kas / Hutang

### Usage (Stock Out)
- Deduct from oldest batch first (by received_at)
- If insufficient: throw InsufficientStockException
- Calculate avg_cost = total_cost / total_qty
- Journal: Dr. Beban Operasional Inventory, Cr. Inventory Asset

### Adjustment
- **Plus:** Create new batch using current average cost
- **Minus:** FIFO deduct same as usage
- Journal: Dr/Cr Inventory Asset, Cr/Dr Selisih Stok

### Transfer (Inter-Branch)
- **Transfer Out:** FIFO deduct from origin → Dr. Inventory In Transit, Cr. Inventory Asset
- **Transfer In:** Create new batch at destination → Dr. Inventory Asset, Cr. Inventory In Transit

## Min Stock Alert

Dashboard widget checks items where total stock across all batches ≤ min_stock per branch.

## UI

```
INVENTORY → Items
┌──────────────────────────────────────────────┐
│  Items Inventaris            [+ Tambah Item] │
├──────────────────────────────────────────────┤
│ [All Category ▼]  [Cari...]                  │
├──────┬──────────┬──────┬──────┬──────┬───────┤
│ Code │ Nama     │ Unit │ Stok │ Min  │ Status│
├──────┼──────────┼──────┼──────┼──────┼───────┤
│ INV01│ Plastik  │ roll │ 2    │ 5    │ ⚠ Low │
│ INV02│ Deterjen │ liter│ 15   │ 10   │ ✓ OK  │
└──────┴──────────┴──────┴──────┴──────┴───────┘

INVENTORY → Stock Detail (per item)
┌──────────────────────────────────────────────┐
│  Plastik — Cabang A                          │
├──────────────────────────────────────────────┤
│  Total Stok: 12 roll                         │
│  Nilai Stok : Rp 120.000                     │
│                                              │
│  Batches:                                    │
│  ├─ BATCH-001 | 01 Jul | 2 roll | Rp 10.000 │
│  ├─ BATCH-002 | 05 Jul | 5 roll | Rp 11.000 │
│  └─ BATCH-003 | 08 Jul | 5 roll | Rp 12.500 │
│                                              │
│  [Stock In] [Stock Out] [Adjust] [Transfer]  │
└──────────────────────────────────────────────┘
```

## Financial Impact

| Transaction | Debit | Credit |
|------------|-------|--------|
| Purchase | Inventory Asset | Kas / Hutang |
| Usage (COGS) | Beban Operasional Inventory | Inventory Asset |
| Adjustment Plus | Inventory Asset | Selisih Stok |
| Adjustment Minus | Selisih Stok | Inventory Asset |
| Transfer Out | Inventory In Transit | Inventory Asset |
| Transfer In | Inventory Asset | Inventory In Transit |

## Files

```
app/Models/InventoryItem.php
app/Models/InventoryBatch.php
app/Models/InventoryTransaction.php
app/Exceptions/InsufficientStockException.php
app/Services/Inventory/InventoryService.php
app/Services/Inventory/FifoService.php
app/Http/Controllers/Web/InventoryItemController.php
app/Http/Controllers/Web/InventoryStockController.php
database/migrations/create_inventory_items_table.php
database/migrations/create_inventory_batches_table.php
database/migrations/create_inventory_transactions_table.php
resources/views/inventory/items/index.blade.php
resources/views/inventory/items/create.blade.php
resources/views/inventory/items/edit.blade.php
resources/views/inventory/stock/index.blade.php
resources/views/inventory/stock/create.blade.php
resources/views/inventory/stock/out.blade.php
resources/views/inventory/stock/transfer.blade.php
```
