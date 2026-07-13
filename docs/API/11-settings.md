# API: Settings

## GET /api/v1/settings

List all global settings (grouped).

## GET /api/v1/settings/{group}

Get settings for a group. **Groups:** `general`, `tax`, `loyalty`, `gateway`, `accounting`, `order`, `notification`, `inventory`

**Response:**
```json
{
    "data": {
        "group": "loyalty",
        "settings": {
            "points_ratio": 1000,
            "points_redeem_rate": 100,
            "points_expiry_days": 90,
            "min_order_amount": 0,
            "auto_upgrade": true
        }
    }
}
```

## PUT /api/v1/settings/{group}

Update settings for a group.

**Request:**
```json
{
    "points_ratio": 800,
    "points_redeem_rate": 100
}
```

## GET /api/v1/branch-settings

Get current branch settings.

## PUT /api/v1/branch-settings

Update branch settings (Branch Admin).
