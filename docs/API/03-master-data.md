# API: Master Data

## GET /api/v1/services

List all services.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "code": "CK",
            "name": "Cuci Kering",
            "unit": "kg",
            "description": "Cuci + kering tanpa setrika",
            "is_active": true
        }
    ]
}
```

## GET /api/v1/services/{id}

Single service detail.

## POST /api/v1/services

Create service. (Dev, SA)

## GET /api/v1/service-pricings

List pricing. **Query:** `?branch_id=1`

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "service_id": 1,
            "service": { "code": "CK", "name": "Cuci Kering" },
            "branch_id": 1,
            "price": 5000,
            "min_weight": 3,
            "is_active": true
        }
    ]
}
```

## PUT /api/v1/service-pricings/{id}

Update pricing. (Branch Admin)

**Request:**
```json
{
    "price": 5500,
    "min_weight": 3
}
```

## PUT /api/v1/service-pricings/bulk

Bulk update pricing for a branch. (Branch Admin)

**Request:**
```json
{
    "branch_id": 1,
    "pricings": [
        { "service_id": 1, "price": 5500 },
        { "service_id": 2, "price": 4500 }
    ]
}
```
