# Module 05: Workshop & Production

## Overview

QR code-based production tracking with sequential status updates. Each item has a unique QR code scanned at every production step.

## Tables

- `order_items` — Has `qr_token` (UUID) for scanning
- `production_statuses` — 8 fixed statuses

## 8 Production Statuses

| Seq | Code | Label | Description |
|-----|------|-------|-------------|
| 1 | `received` | Terima | Barcode scan & label |
| 2 | `washed` | Cuci | Wash cycle |
| 3 | `dried` | Kering | Dry process |
| 4 | `ironed` | Lipat | Fold / Iron |
| 5 | `packed` | Cek | Quality gate |
| 6 | `ready_for_pickup` | Siap | Ready for pickup |
| 7 | `picked_up` | Diambil | Picked up by customer |
| 8 | `cancelled` | Dibatalkan | Cancelled |

### Rules

- Forward-only: can only advance to next status
- Cannot skip statuses
- Cannot go backwards
- Throws error on invalid transition

### QR Scan Flow

```
Workshop Staff scans QR on item label
         ↓
System looks up order_item by qr_token
         ↓
Shows ALL items in the same order
         ↓
Staff can see current status of each item
         ↓
Staff scans again (or clicks) to advance status
         ↓
If valid transition → update status
         ↓
Show WA Notification modal
         ↓
Staff clicks WA link → opens wa.me in new tab
         ↓
Staff confirms → transaction saved
```

### WA Notification Modal

```
┌──────────────────────────────────────┐
│  ✅ Status Berhasil Diperbarui!      │
│                                      │
│  Item: CK — 3 kg                     │
│  Status: Cuci → Kering               │
│                                      │
│  Aksi Selanjutnya:                   │
│  Kirim notifikasi ke customer:       │
│                                      │
│  [📱 Kirim WA ke Customer]           │
│                                      │
│  Setelah WA terkirim, klik "OK"     │
│                                      │
│  [OK, Sudah Dikirim]  [Nanti Saja]   │
└──────────────────────────────────────┘
```

## UI

```
WORKSHOP → Scan
┌──────────────────────────────────────────────┐
│  Scan QR Code                                │
│                                              │
│  ┌──────────────────────────────────┐        │
│  │                                  │        │
│  │   [Camera Scanner Zone]         │        │
│  │                                  │        │
│  └──────────────────────────────────┘        │
│                                              │
│  Atau masukkan kode: [________________] [Cari]│
└──────────────────────────────────────────────┘

WORKSHOP → Order Detail (after scan)
┌──────────────────────────────────────────────┐
│  Order: CAB-20260709-00001                   │
│  Pelanggan: Bpk. Amir                        │
├──────────────────────────────────────────────┤
│  Items:                                      │
│  ┌──────┬───────┬──────────────┬──────────┐  │
│  │ Item │ Qty   │ Status       │ Aksi     │  │
│  ├──────┼───────┼──────────────┼──────────┤  │
│  │ CK   │ 3 kg  │ ●●○•••••    │ [Next ▼] │  │
│  │ ST   │ 2 kg  │ ●●●○••••    │ [Next ▼] │  │
│  └──────┴───────┴──────────────┴──────────┘  │
│                                              │
│  Status Timeline:                            │
│  Terima → Cuci → Kering → Lipat → Cek →     │
│  Siap → Diambil                              │
│  ●●●○○○○○  (3/8 completed)                 │
└──────────────────────────────────────────────┘
```

## Routes

```php
Route::get('workshop', [WorkshopController::class, 'index'])->name('workshop.index');
Route::get('workshop/scan', [WorkshopController::class, 'scan'])->name('workshop.scan');
Route::post('workshop/scan', [WorkshopController::class, 'lookup']);
Route::get('workshop/order/{order}', [WorkshopController::class, 'show'])->name('workshop.show');
Route::put('workshop/items/{item}/status', [WorkshopController::class, 'updateStatus']);
```

## Files

```
app/Models/ProductionStatus.php
app/Enums/ProductionStatus.php
app/Exceptions/InvalidStatusTransitionException.php
app/Services/Workshop/WorkshopService.php
app/Services/Workshop/StatusTransitionService.php
app/Http/Controllers/Web/WorkshopController.php
database/migrations/create_production_statuses_table.php
database/seeders/ProductionStatusSeeder.php
resources/views/workshop/scan.blade.php
resources/views/workshop/order-detail.blade.php
```
