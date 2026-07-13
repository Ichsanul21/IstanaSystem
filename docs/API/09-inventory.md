# API: Inventory

## GET /api/v1/inventory/items

List inventory items. **Query:** `?category=&search=&is_active=`

## GET /api/v1/inventory/items/{id}

Single item with stock summary.

## POST /api/v1/inventory/items

Create item.

## PUT /api/v1/inventory/items/{id}

Update item.

## GET /api/v1/inventory/stock

Current stock per branch. **Query:** `?branch_id=&item_id=&low_only=`

**Response:**
```json
{
    "data": [
        {
            "item_id": 1,
            "item": { "code": "INV-01", "name": "Plastik", "unit": "roll" },
            "branch_id": 1,
            "total_quantity": 12,
            "total_value": 120000,
            "min_stock": 5,
            "is_low": false
        }
    ]
}
```

## GET /api/v1/inventory/stock/{itemId}/detail

Stock detail per batch.

**Response:**
```json
{
    "data": {
        "item": { "name": "Plastik" },
        "total_quantity": 12,
        "total_value": 120000,
        "batches": [
            { "batch_code": "BATCH-001", "quantity": 2, "unit_cost": 10000, "received_at": "2026-07-01" },
            { "batch_code": "BATCH-002", "quantity": 5, "unit_cost": 11000, "received_at": "2026-07-05" },
            { "batch_code": "BATCH-003", "quantity": 5, "unit_cost": 12500, "received_at": "2026-07-08" }
        ]
    }
}
```

## POST /api/v1/inventory/stock/in

Stock in (purchase).

**Request:**
```json
{
    "item_id": 1,
    "branch_id": 1,
    "quantity": 10,
    "unit_cost": 12500,
    "received_at": "2026-07-09",
    "note": "Pembelian bulanan"
}
```

## POST /api/v1/inventory/stock/out

Stock out (usage).

**Request:**
```json
{
    "item_id": 1,
    "branch_id": 1,
    "quantity": 3,
    "note": "Pemakaian shift 1"
}
```

## POST /api/v1/inventory/stock/adjust

Stock adjustment.

**Request:**
```json
{
    "item_id": 1,
    "branch_id": 1,
    "type": "plus",
    "quantity": 2,
    "note": "Opname fisik"
}
```

## POST /api/v1/inventory/stock/transfer

Inter-branch transfer.

**Request:**
```json
{
    "item_id": 1,
    "from_branch_id": 1,
    "to_branch_id": 2,
    "quantity": 5,
    "note": "Transfer stok"
}
```

## GET /api/v1/inventory/alerts

Low stock alerts for current branch.
