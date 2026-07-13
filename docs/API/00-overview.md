# API Overview

## Base URL

| Environment | URL |
|-------------|-----|
| Development | `http://localhost:8000/api/v1` |
| Production | `https://api.istanalaundry.com/api/v1` |

## Authentication

| Method | Type | Header |
|--------|------|--------|
| Web | Session cookie | Laravel session |
| API | Bearer token | `Authorization: Bearer {token}` |

### Token Generation
```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "secret"
}

Response:
{
    "token": "1|abc123...",
    "user": { ... }
}
```

## Response Format

### Success
```json
{
    "success": true,
    "data": { ... },
    "message": "Data berhasil dimuat"
}
```

### Error
```json
{
    "success": false,
    "message": "Unauthenticated",
    "errors": null
}
```

### Pagination
```json
{
    "success": true,
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 10,
        "per_page": 15,
        "total": 150
    }
}
```

## API Versioning

- Version prefix: `/api/v1/`
- Breaking changes → new version (`/api/v2/`)
- Non-breaking → additive changes within version

## Rate Limiting

| Route Group | Limit |
|-------------|-------|
| Authenticated | 60 req/min |
| Public (tracking) | 30 req/min |
| Webhook | 120 req/min |

## Standard Headers

```http
Accept: application/json
Content-Type: application/json
X-Requested-With: XMLHttpRequest
Authorization: Bearer {token}  (for API)
X-Branch-Id: 1                 (optional branch context)
```

## Common Status Codes

| Code | Description |
|------|-------------|
| 200 | OK |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthenticated |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Server Error |

## API Modules

| Module | Base Path | Auth |
|--------|-----------|------|
| [Auth](01-auth.md) | `/auth` | No |
| [Branches](02-branches.md) | `/branches` | Yes |
| [Master Data](03-master-data.md) | `/services`, `/service-pricings` | Yes |
| [Orders](04-orders.md) | `/orders` | Yes |
| [Workshop](05-workshop.md) | `/workshop` | Yes |
| [Customers](06-customers.md) | `/customers` | Yes |
| [Promotions](07-promotions.md) | `/promotions` | Yes |
| [Finance](08-finance.md) | `/finance` | Yes |
| [Inventory](09-inventory.md) | `/inventory` | Yes |
| [Dashboard](10-dashboard.md) | `/dashboard` | Yes |
| [Settings](11-settings.md) | `/settings` | Yes |
| [Payment Gateway](12-payment-gateway.md) | `/payments` | Mixed |
| [Customer Tracking](13-customer-tracking.md) | `/track` | No |
