# Module 12: Midtrans Payment Gateway

## Overview

Integration with Midtrans Snap API (formerly Veritrans) for online payments via Virtual Account, QRIS, E-Wallet, and Credit Card.

## Tables

- `gateway_configurations` — Single row for Midtrans credentials (encrypted)
- `gateway_payments` — Payment transaction records

## Supported Payment Methods (via Midtrans)

| Method | Type | Notes |
|--------|------|-------|
| Virtual Account | `bank_transfer` | BCA, Mandiri, BNI, BRI, Permata |
| E-Wallet | `gopay`, `shopeepay`, `ovo` | |
| QRIS | `qris` | All standard QR |
| Credit Card | `credit_card` | Visa/Mastercard (may need MDR) |
| Convenience Store | `cstore` | Indomaret, Alfamart |

## Configuration

```php
// Settings → Payment Gateway
[
    'is_active' => true/false,
    'is_production' => true/false,
    'merchant_id' => 'M123...',
    'client_key' => 'Midtrans-Client-...',
    'server_key' => 'Midtrans-Server-...',
]
```

## Payment Flow

```
1. POS → Total: Rp 44.400
2. Cashier selects "Bayar Online"
3. System calls Midtrans Snap API:
   - transaction_details: { order_id, gross_amount }
   - customer_details: { name, phone }
   - callbacks: finish, unfinish, error URLs
4. Midtrans returns Snap Token
5. Snap popup opens → Customer chooses payment method
6. Customer completes payment on Midtrans page
7. Midtrans sends webhook (POST) to /api/payments/midtrans/callback
8. System updates gateway_payments + order payment_status
9. If success → auto-journal + earn points + WA notification
```

## Webhook Handler

```php
// Route: POST /api/payments/midtrans/callback (no CSRF)
// Signature verification:

$signature = hash('sha512', 
    $request->order_id . $request->status_code . 
    $request->gross_amount . $serverKey
);

// Status mapping:
match ($request->transaction_status) {
    'settlement', 'capture' => 'success',
    'pending' => 'pending',
    'deny', 'cancel', 'expire' => 'failed',
    'refund', 'partial_refund' => 'refund',
};
```

## Manual Verification (Fallback)

```php
// When webhook fails, CS can click "Verifikasi Pembayaran"
public function checkPaymentStatus(Order $order): void
{
    $payment = $order->gatewayPayment;
    $status = Midtrans::transaction()->status($payment->transaction_id);
    
    if ($status->transaction_status === 'settlement' && !$payment->isSuccess()) {
        $payment->markSuccess($status);
        event(new OrderPaidViaGateway($order, $payment));
    }
}
```

## UI in POS

```
Metode Bayar:
◉ Cash
○ Transfer (manual)
○ QRIS (manual)
○ [Gateway] Bayar Online
     ↓
[Proses Pembayaran] → Snap popup opens
     ↓
Snap Popup (iframed or redirect):
┌──────────────────────────┐
│  Midtrans Payment        │
│                          │
│  [Virtual Account ▼]     │
│  [E-Wallet ▼]            │
│  [QRIS]                  │
│  [Credit Card]           │
│                          │
│  Total: Rp 44.400        │
│                          │
│  [Pilih Pembayaran]      │
└──────────────────────────┘
```

## Files

```
app/Models/GatewayConfiguration.php
app/Models/GatewayPayment.php
app/Services/Payment/PaymentGatewayInterface.php
app/Services/Payment/MidtransService.php
app/Http/Controllers/Api/V1/PaymentWebhookController.php
app/Http/Controllers/Web/GatewayConfigurationController.php
database/migrations/create_gateway_configurations_table.php
database/migrations/create_gateway_payments_table.php
resources/views/settings/payment-gateway.blade.php
resources/views/pos/partials/gateway-payment.blade.php
```
