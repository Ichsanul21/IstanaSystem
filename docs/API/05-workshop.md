# API: Workshop

> All responses use the standard ApiResponse envelope: `{"success": true, "data": ..., "message": "..."}`.
> See [00-overview.md](00-overview.md) for the full response format spec.

## GET /api/v1/workshop/scan/{qrToken}

Scan QR code on item label.

**Request:**
```http
GET /api/v1/workshop/scan/abc123def456
Authorization: Bearer {token}
X-Branch-Id: 1
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "item": {
            "id": 1,
            "service": { "code": "CK", "name": "Cuci Kering" },
            "quantity": 3,
            "current_status": { "id": 3, "code": "CUCI", "name": "Cuci", "sequence": 3 },
            "next_status": { "id": 4, "code": "KERING", "name": "Kering", "sequence": 4 }
        },
        "order": {
            "id": 1,
            "order_number": "CAB-20260709-00001",
            "customer_name": "Bpk. Amir",
            "items": [
                { "service": { "code": "CK" }, "quantity": 3, "status": "CUCI" },
                { "service": { "code": "ST" }, "quantity": 2, "status": "KERING" }
            ]
        }
    },
    "message": "QR code berhasil dipindai"
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "QR code tidak valid atau item tidak ditemukan",
    "errors": null
}
```

## POST /api/v1/workshop/scan/{qrToken}/update

Update item status.

**Request:**
```http
POST /api/v1/workshop/scan/abc123def456/update
Authorization: Bearer {token}
X-Branch-Id: 1
Content-Type: application/json
Accept: application/json
```

```json
{
    "note": "Cuci normal, noda membandel"
}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "new_status": { "id": 4, "code": "KERING", "name": "Kering", "sequence": 4 },
        "wa_link": "https://wa.me/62812xxxx?text=Halo...",
        "wa_required": true
    },
    "message": "Status item berhasil diperbarui"
}
```

## GET /api/v1/workshop/queue

Get workshop queue (all items in production for this workshop).

**Query:** `?status=&search=`

**Request:**
```http
GET /api/v1/workshop/queue?status=CUCI&search=Amir
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
            "order_number": "CAB-20260709-00001",
            "item": "Cuci Kering - 3 kg",
            "current_status": "CUCI",
            "customer": "Bpk. Amir",
            "elapsed_time": "2h 15m"
        }
    ],
    "message": "Data berhasil dimuat"
}
```

## GET /api/v1/workshop/stats

Production statistics (for dashboard).

**Request:**
```http
GET /api/v1/workshop/stats
Authorization: Bearer {token}
X-Branch-Id: 1
Accept: application/json
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "total_in_production": 45,
        "completed_today": 28,
        "average_time": "3h 12m",
        "by_status": {
            "TERIMA": 5,
            "PILAH": 8,
            "CUCI": 12,
            "KERING": 10,
            "LIPAT": 7,
            "CEK": 3
        }
    },
    "message": "Data berhasil dimuat"
}
```
