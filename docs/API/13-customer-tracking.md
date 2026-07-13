# API: Customer Tracking

These endpoints are **public** (no auth). Rate limited.

## GET /api/v1/track/{token}

Get tracking data for an order.

**Query:** `?pin=12` (last 2 digits of phone)

**Response (verified):**
```json
{
    "success": true,
    "data": {
        "order_number": "CAB-20260709-00001",
        "customer_name": "Bpk. Amir",
        "status": "process",
        "current_step": 3,
        "total_steps": 8,
        "estimated_finish": "2026-07-09T14:30:00Z",
        "payment_status": "paid",
        "grand_total": 21000,
        "timeline": [
            { "name": "Terima", "code": "TERIMA", "sequence": 1, "completed": true, "time": "09:30" },
            { "name": "Pilah", "code": "PILAH", "sequence": 2, "completed": true, "time": "09:45" },
            { "name": "Cuci", "code": "CUCI", "sequence": 3, "completed": true, "time": "10:15" },
            { "name": "Kering", "code": "KERING", "sequence": 4, "completed": false, "time": null },
            { "name": "Lipat", "code": "LIPAT", "sequence": 5, "completed": false, "time": null },
            { "name": "Cek", "code": "CEK", "sequence": 6, "completed": false, "time": null },
            { "name": "Siap", "code": "SIAP", "sequence": 7, "completed": false, "time": null },
            { "name": "Diambil", "code": "DIAMBIL", "sequence": 8, "completed": false, "time": null }
        ],
        "items": [
            { "service": "CK", "name": "Cuci Kering", "quantity": "3 kg", "current_status": "CUCI" },
            { "service": "ST", "name": "Setrika", "quantity": "2 kg", "current_status": "KERING" }
        ],
        "branch": { "name": "Cabang A", "phone": "0812xxxx", "address": "Jl. Hidayatullah" }
    }
}
```

## POST /api/v1/track/{token}/verify

Verify PIN.

**Request:**
```json
{
    "pin": "89"
}
```

**Response (200):**
```json
{
    "success": true,
    "data": { "verified": true }
}
```

**Response (422):**
```json
{
    "success": false,
    "message": "PIN salah"
}
```
