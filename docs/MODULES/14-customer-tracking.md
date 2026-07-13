# Module 14: Customer Tracking

## Overview

Public tracking page for customers to view their order status in real-time. No login required — accessed via unique token sent through WhatsApp.

## Tables

- `orders.tracking_token` — UUID v4, unique per order
- No additional tables needed (uses existing order + order_item + status_log relations)

## How It Works

```
1. Order created → system generates tracking_token (UUID)
2. Token embedded in WA notification sent to customer
3. Customer clicks link: https://domain.com/track/{token}
4. System asks for PIN (last 2 digits of registered phone)
5. Customer enters PIN → sees order status
```

## Public Page: `/track/{token}`

### PIN Verification

```php
// PIN = last 2 digits of phone number
// If phone = "08123456789" → PIN = "89"

public function verifyPin(Request $request, string $token): JsonResponse
{
    $order = Order::where('tracking_token', $token)->firstOrFail();
    $phone = $order->customer->phone ?? $order->customer_phone;
    $expectedPin = substr($phone, -2);
    
    if ($request->pin !== $expectedPin) {
        return response()->json(['error' => 'PIN salah'], 422);
    }
    
    session(["tracking_{$token}" => true]);
    return response()->json(['success' => true]);
}
```

### Page Content (after PIN verified)

```
┌──────────────────────────────────────────────┐
│              ISTANA LAUNDRY                   │
│         Tracking Pesanan Anda                 │
├──────────────────────────────────────────────┤
│                                              │
│  Order: CAB-20260709-00001                   │
│  Pelanggan: Bpk. Amir                        │
│  Status: 🟡 Proses (3/8)                     │
│                                              │
│  Timeline:                                   │
│  ● TERIMA    — 09 Jul 09:30 ✓               │
│  ● PILAH     — 09 Jul 09:45 ✓               │
│  ● CUCI      — 09 Jul 10:30 ◉ (current)     │
│  ○ KERING    —                                │
│  ○ LIPAT     —                                │
│  ○ CEK       —                                │
│  ○ SIAP      —                                │
│  ○ DIAMBIL   —                                │
│                                              │
│  Items:                                      │
│  ┌──────────┬──────┬──────────────┐         │
│  │ Layanan  │ Qty  │ Status       │         │
│  ├──────────┼──────┼──────────────┤         │
│  │ CK       │ 3 kg │ CUCI         │         │
│  │ ST       │ 2 kg │ KERING       │         │
│  └──────────┴──────┴──────────────┘         │
│                                              │
│  Estimasi Selesai: 09 Jul 2026 14:30        │
│                                              │
│  Ada pertanyaan? Hubungi kami:               │
│  [📱 WhatsApp 0812-XXXX-XXXX]               │
│                                              │
└──────────────────────────────────────────────┘
```

### Design Notes

- Must match Istana Laundry branding: black + orange, Inter font
- Mobile-first responsive
- No navigation/sidebar (public page)
- Minimal, focused on order status only
- WA link for customer to contact branch

## WA Notification on Status Updates

Each time production status changes, system generates a tracking WA link:

```
Halo Kak {name},

Status pesanan Anda telah berubah:

📦 Pesanan: #CAB-20260709-00001
📍 Status saat ini: CUCI

Pantau terus pesanan Anda di sini:
{tracking_url}

Terima kasih telah menggunakan Istana Laundry ❤️
```

## Security

| Concern | Mitigation |
|---------|-----------|
| Token guessing | UUID v4 (128-bit random, unguessable) |
| PIN brute force | Rate limit: 5 attempts per token per 15 min |
| Data exposure | Only order + items + status; no payment info |
| Session | Session expires after 1 hour or browser close |

## Routes

```php
// RouteServiceProvider: public route, no auth, no CSRF for GET
Route::get('/track/{token}', [TrackingController::class, 'show'])->name('tracking.show');
Route::post('/track/{token}/verify', [TrackingController::class, 'verifyPin'])->name('tracking.verify');
```

## Files

```
app/Http/Controllers/Web/TrackingController.php
app/Services/Tracking/TrackingService.php
resources/views/tracking/show.blade.php
resources/views/tracking/partials/timeline.blade.php
resources/views/tracking/partials/items.blade.php
resources/views/tracking/partials/pin-form.blade.php
```
