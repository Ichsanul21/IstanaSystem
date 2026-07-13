# API: Orders

## GET /api/v1/orders

List orders. **Query:** `?search=&status=&payment_status=&date_from=&date_to=&customer_id=&branch_id=&per_page=15`

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "order_number": "CAB-20260709-00001",
            "customer": { "id": 1, "name": "Bpk. Amir" },
            "customer_name": "Bpk. Amir",
            "customer_phone": "0812xxxx",
            "total_amount": 23000,
            "discount_amount": 2000,
            "grand_total": 21000,
            "status": "process",
            "payment_status": "paid",
            "payment_method": "cash",
            "items": [
                { "id": 1, "service": { "code": "CK", "name": "Cuci Kering" }, "quantity": 3, "unit": "kg", "price": 5000, "subtotal": 15000 }
            ],
            "created_at": "2026-07-09T10:00:00Z"
        }
    ],
    "meta": { "current_page": 1, "last_page": 5, "total": 68 }
}
```

## GET /api/v1/orders/{id}

Single order with items and status logs.

## POST /api/v1/orders

Create order (POS).

**Request:**
```json
{
    "customer_id": 1,
    "customer_name": "Bpk. Amir",
    "customer_phone": "0812xxxx",
    "items": [
        { "service_id": 1, "quantity": 3 },
        { "service_id": 3, "quantity": 2 }
    ],
    "promotion_id": 1,
    "redeem_points": 0,
    "notes": "Hati-hati, bahan mudah luntur"
}
```

**Response (201):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "order_number": "CAB-20260709-00001",
        "grand_total": 21000
    }
}
```

## POST /api/v1/orders/{id}/payment

Process payment.

**Request:**
```json
{
    "method": "cash",
    "amount": 25000,
    "reference": null
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "paid_amount": 25000,
        "change_amount": 4000,
        "payment_status": "paid"
    }
}
```

## POST /api/v1/orders/{id}/refund

Submit refund request.

**Request:**
```json
{
    "amount": 21000,
    "reason": "Customer cancel"
}
```

## POST /api/v1/orders/{id}/receipt

Generate receipt PDF.

## GET /api/v1/orders/{id}/tracking-status

Get current production status summary for customer tracking.
