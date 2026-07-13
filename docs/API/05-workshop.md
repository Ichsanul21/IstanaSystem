# API: Workshop

## GET /api/v1/workshop/scan/{qrToken}

Scan QR code on item label.

**Response:**
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
    }
}
```

## POST /api/v1/workshop/scan/{qrToken}/update

Update item status.

**Request:**
```json
{
    "note": "Cuci normal, noda membandel"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "new_status": { "code": "KERING", "name": "Kering", "sequence": 4 },
        "wa_link": "https://wa.me/62812xxxx?text=Halo...",
        "wa_required": true
    }
}
```

## GET /api/v1/workshop/queue

Get workshop queue (all items in production for this workshop).

**Query:** `?status=&search=`

**Response:**
```json
{
    "data": [
        {
            "order_number": "CAB-20260709-00001",
            "item": "Cuci Kering - 3 kg",
            "current_status": "CUCI",
            "customer": "Bpk. Amir",
            "elapsed_time": "2h 15m"
        }
    ]
}
```

## GET /api/v1/workshop/stats

Production statistics (for dashboard).

**Response:**
```json
{
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
    }
}
```
