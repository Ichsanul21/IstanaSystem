# API: Customers

## GET /api/v1/customers

List customers. **Query:** `?search=&tier_id=&is_member=&sort_by=name`

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "code": "CUS-00001",
            "name": "Bpk. Amir",
            "phone": "0812xxxx",
            "is_member": true,
            "tier": { "id": 3, "name": "Gold", "level": 3 },
            "total_points": 1240,
            "total_purchase": 1240000,
            "total_orders": 12,
            "last_order_at": "2026-07-08T14:00:00Z"
        }
    ]
}
```

## GET /api/v1/customers/{id}

Single customer with all data (info, orders, points, notes).

## POST /api/v1/customers

Create customer. (Admin/CS only)

## PUT /api/v1/customers/{id}

Update customer.

## GET /api/v1/customers/{id}/orders

Customer order history.

## GET /api/v1/customers/{id}/points

Customer point transactions.

## POST /api/v1/customers/{id}/adjust-points

Manual point adjustment. (Admin)

**Request:**
```json
{
    "points": -100,
    "description": "Koreksi poin"
}
```

## GET /api/v1/membership-tiers

List tiers.

## GET /api/v1/customers/lookup

Fast lookup by name or phone for POS.

**Query:** `?q=0812`

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Bpk. Amir",
            "phone": "0812xxxx",
            "tier_name": "Gold",
            "available_points": 1240
        }
    ]
}
```
