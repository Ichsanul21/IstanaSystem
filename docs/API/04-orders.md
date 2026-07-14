# API: Orders

> All responses use the standard ApiResponse envelope: `{"success": true, "data": ..., "message": "..."}`.
> See [00-overview.md](00-overview.md) for the full response format spec.

## GET /api/v1/orders

List orders. **Query:** `?search=&status=&payment_status=&date_from=&date_to=&customer_id=&branch_id=&per_page=15`

**Request:**
```http
GET /api/v1/orders?status=processing&per_page=15
Authorization: Bearer {token}
X-Branch-Id: 1
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
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
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 68
    },
    "message": "Data berhasil dimuat"
}
```

**Error Response (401):**
```json
{
    "success": false,
    "message": "Unauthenticated",
    "errors": null
}
```

## GET /api/v1/orders/{id}

Single order with items and status logs.

**Request:**
```http
GET /api/v1/orders/1
Authorization: Bearer {token}
X-Branch-Id: 1
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "order_number": "CAB-20260709-00001",
        "customer": { "id": 1, "name": "Bpk. Amir" },
        "total_amount": 23000,
        "discount_amount": 2000,
        "grand_total": 21000,
        "status": "process",
        "payment_status": "paid",
        "items": [ ... ],
        "status_logs": [ ... ],
        "created_at": "2026-07-09T10:00:00Z"
    },
    "message": "Data berhasil dimuat"
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Order tidak ditemukan",
    "errors": null
}
```

## POST /api/v1/orders

Create order (POS).

**Request:**
```http
POST /api/v1/orders
Authorization: Bearer {token}
X-Branch-Id: 1
Content-Type: application/json
Accept: application/json
```

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
    },
    "message": "Order berhasil dibuat"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "items": ["Minimal 1 item harus diisi"],
        "customer_name": ["Nama customer wajib diisi"]
    }
}
```

## POST /api/v1/orders/{id}/payment

Process payment.

**Request:**
```http
POST /api/v1/orders/1/payment
Authorization: Bearer {token}
X-Branch-Id: 1
Content-Type: application/json
Accept: application/json
```

```json
{
    "method": "cash",
    "amount": 25000,
    "reference": null
}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "paid_amount": 25000,
        "change_amount": 4000,
        "payment_status": "paid"
    },
    "message": "Pembayaran berhasil diproses"
}
```

## POST /api/v1/orders/{id}/refund

Submit refund request.

**Request:**
```http
POST /api/v1/orders/1/refund
Authorization: Bearer {token}
X-Branch-Id: 1
Content-Type: application/json
Accept: application/json
```

```json
{
    "amount": 21000,
    "reason": "Customer cancel"
}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "refund_id": 1,
        "amount": 21000,
        "status": "pending"
    },
    "message": "Refund berhasil diajukan"
}
```

## POST /api/v1/orders/{id}/receipt

Generate receipt PDF.

**Request:**
```http
POST /api/v1/orders/1/receipt
Authorization: Bearer {token}
X-Branch-Id: 1
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "pdf_url": "/storage/receipts/CAB-20260709-00001.pdf"
    },
    "message": "Receipt berhasil dibuat"
}
```

## GET /api/v1/orders/{id}/tracking-status

Get current production status summary for customer tracking.

**Request:**
```http
GET /api/v1/orders/1/tracking-status
Authorization: Bearer {token}
X-Branch-Id: 1
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "order_number": "CAB-20260709-00001",
        "current_status": "CUCI",
        "timeline": [ ... ]
    },
    "message": "Data berhasil dimuat"
}
```
