# API: Promotions

## GET /api/v1/promotions

List promotions. **Query:** `?is_active=true&branch_id=1&type=percentage`

## GET /api/v1/promotions/{id}

Single promotion detail.

## POST /api/v1/promotions

Create promotion.

## PUT /api/v1/promotions/{id}

Update promotion.

## GET /api/v1/promotions/eligible/{orderId}

Get eligible promotions for a specific order.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "code": "LBR26",
            "name": "Diskon Lebaran 2026",
            "type": "percentage",
            "value": 10,
            "max_discount": 5000,
            "estimated_discount": 2100
        }
    ]
}
```

## POST /api/v1/promotions/{id}/calculate

Calculate discount for an order.

**Request:**
```json
{
    "order_id": 1,
    "redeem_points": 0
}
```

**Response:**
```json
{
    "data": {
        "subtotal": 23000,
        "promotion_discount": 2000,
        "point_discount": 0,
        "grand_total": 21000
    }
}
```
