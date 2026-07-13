# API: Payment Gateway

## POST /api/v1/payments/midtrans/snap

Get Snap token for Midtrans transaction.

**Request:**
```json
{
    "order_id": 1
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "snap_token": "snap-xxx-yyy-zzz",
        "snap_redirect_url": "https://app.midtrans.com/snap/v2/vtweb/xxx"
    }
}
```

## POST /api/v1/payments/midtrans/callback

Midtrans webhook (no auth required).

**Request:** (sent by Midtrans)
```json
{
    "transaction_id": "trx-xxx",
    "order_id": "order-xxx",
    "transaction_status": "settlement",
    "status_code": "200",
    "gross_amount": "21000.00",
    "payment_type": "bank_transfer",
    "signature_key": "hash..."
}
```

**Response:**
```json
{
    "ok": true
}
```

## GET /api/v1/payments/{orderId}/status

Check payment status.

**Response:**
```json
{
    "data": {
        "transaction_id": "trx-xxx",
        "status": "success",
        "payment_type": "bank_transfer",
        "paid_at": "2026-07-09T10:30:00Z"
    }
}
```

## POST /api/v1/payments/{orderId}/verify

Manual verification (CS fallback).
