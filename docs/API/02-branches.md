# API: Branches

All endpoints require `Authorization: Bearer {token}`.

## GET /api/v1/branches

List all branches.

**Query params:** `?search=&is_active=true`

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "code": "CAB-001",
            "name": "Cabang A",
            "workshop": { "id": 1, "name": "Workshop Pusat" },
            "address": "Jl. Hidayatullah",
            "phone": "0812xxxx",
            "opening_time": "08:00",
            "closing_time": "21:00",
            "is_active": true,
            "daily_capacity": 50
        }
    ]
}
```

## GET /api/v1/branches/{id}

Single branch detail.

## POST /api/v1/branches

Create branch. (Dev, SA only)

## PUT /api/v1/branches/{id}

Update branch. (Dev, SA, BA)

## GET /api/v1/branches/{id}/daily-cash-flow

Get daily cash flow for a branch.

**Query params:** `?date=2026-07-09`

## GET /api/v1/workshops

List workshops.

## GET /api/v1/branches/switch/{id}

Switch active branch context (for multi-branch roles).
