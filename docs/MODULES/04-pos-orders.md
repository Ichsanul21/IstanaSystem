# Module 04: POS & Orders

## Overview

Point of Sale for creating laundry orders, processing payments, handling refunds, and printing receipts.

## Tables

- `orders` — Order header
- `order_items` — Per-service line items
- `payments` — Payment records
- `refunds` — Refund requests (4-step flow)

## Order Number Format

```
{BranchCode}-{YYYYMMDD}-{XXXXX}

Example: CAB-20260709-00001

- BranchCode: from branches.code
- YYYYMMDD: order date
- XXXXX: daily auto-increment (reset per branch per day)
```

## Order Status Lifecycle

```
DRAFT → PENDING → RECEIVED → WASHED → DRIED → IRONED → PACKED → READY_FOR_PICKUP → PICKED_UP
    ↓
CANCELLED (can happen at any point before picked_up)
```

| Status | Description |
|--------|-------------|
| `draft` | Order being created, items being added |
| `pending` | Order submitted, waiting for payment |
| `received` | Paid, received by workshop — production starts |
| `washed` | Wash cycle |
| `dried` | Dry process |
| `ironed` | Fold / Iron |
| `packed` | Quality check |
| `ready_for_pickup` | Ready for customer pickup |
| `picked_up` | Picked up by customer — terminal |
| `cancelled` | Cancelled — terminal |

> Order status from `received` onward follows the same workflow as workshop production statuses. Status transitions are forward-only and sequential.

## Payment Methods

| Method | Code | Notes |
|--------|------|-------|
| Cash | `cash` | Receive cash, calculate change |
| Transfer | `transfer` | Manual transfer, input reference |
| QRIS | `qris` | QR scan payment |
| Gateway | `gateway` | Midtrans Snap (online payment) |

## POS Flow

```
1. Pilih/Ketik Pelanggan (search by name/phone, or walk-in)
         ↓
2. Pilih Layanan + Quantity (kg/pcs/m²)
         ↓
3. Promo (optional) → pilih promo atau pakai poin
         ↓
4. Hitung Total → subtotal - diskon - poin = grand total
         ↓
5. Pembayaran → pilih metode, input jumlah bayar
         ↓
6. Order Created → generate QR token per item
         ↓
7. Print receipt → thermal printer / PDF
```

## Refund Flow (4 Steps)

```
1. CASHIER submits refund request
   → refund.status = 'requested'
         ↓
2. CS follows up with customer via WA
   → refund.status stays, CS adds note
         ↓
3. BRANCH ADMIN approves or rejects
   → refund.status = 'approved' | 'rejected'
         ↓
4. CASHIER completes refund (give money back)
   → refund.status = 'completed'
   → Order payment_status = 'refunded'
```

## Receipt Design

```
ISTANA LAUNDRY
Jl. Hidayatullah, Samarinda
Telp: 0812-XXXX-XXXX
=============================
Order: CAB-20260709-00001
Tgl  : 09 Jul 2026 14:30
Kasir: Budi
=============================
CK 3 kg  x Rp5.000 = Rp15.000
ST 2 kg  x Rp4.000 = Rp 8.000
-----------------------------
Subtotal          Rp23.000
Diskon Promo     -Rp 2.000
-----------------------------
Grand Total      Rp21.000
Tunai            Rp25.000
Kembali          Rp 4.000
=============================
Status: ✅ LUNAS
=============================
Terima kasih telah menggunakan
Istana Laundry ❤️
```

## UI

```
POS → New Order
┌──────────────────────────────────────────────┐
│  POS — Cabang A                               │
├──────────────────────────────────────────────┤
│ Pelanggan: [Cari...]  [Walk-in]               │
├──────────────────────────────────────────────┤
│ Layanan           │ Qty  │ Harga │ Subtotal   │
├───────────────────┼──────┼───────┼───────────┤
│ Cuci Kering       │ 3 kg │ 5.000 │ 15.000    │
│ Setrika           │ 2 kg │ 4.000 │ 8.000     │
│ [+ Tambah Item]   │      │       │           │
├───────────────────┴──────┴───────┴───────────┤
│ Subtotal                   Rp 23.000          │
│ Diskon Promo              -Rp 2.000           │
│ Diskon Poin               Rp 0                │
│ Grand Total               Rp 21.000           │
├──────────────────────────────────────────────┤
│ Metode Bayar:                                 │
│ ◉ Cash  ○ Transfer  ○ QRIS  ○ Gateway         │
│                                               │
│ Dibayar: [25.000]                             │
│ Kembali: Rp 4.000                             │
│                                               │
│ [Bayar & Cetak]     [Simpan Draft]            │
└──────────────────────────────────────────────┘
```

## Routes

```php
Route::resource('orders', OrderController::class);
Route::resource('orders.payments', PaymentController::class);
Route::resource('orders.refunds', RefundController::class);
Route::get('pos', [POSController::class, 'index'])->name('pos.index');
Route::get('orders/{order}/receipt', [OrderController::class, 'receipt']);
```

## Files

```
app/Models/Order.php
app/Models/OrderItem.php
app/Models/Payment.php
app/Models/Refund.php
app/Traits/GeneratesOrderNumber.php
app/Services/Order/OrderService.php
app/Services/Order/PaymentService.php
app/Services/Order/RefundService.php
app/Http/Controllers/Web/OrderController.php
app/Http/Controllers/Web/PaymentController.php
app/Http/Controllers/Web/RefundController.php
database/migrations/create_orders_table.php
database/migrations/create_order_items_table.php
database/migrations/create_payments_table.php
database/migrations/create_refunds_table.php
resources/views/pos/index.blade.php
resources/views/orders/index.blade.php
resources/views/orders/show.blade.php
resources/views/orders/receipt.blade.php
```
