# Module 05: Workshop & Production

## Overview

QR code-based production tracking with sequential status updates. Each item has a unique QR code scanned at every production step.

## Tables

- `order_items` — Has `qr_token` (UUID) for scanning
- `production_statuses` — 8 fixed statuses

## 8 Production Statuses

| Seq | Code | Label | Description |
|-----|------|-------|-------------|
| 1 | `TERIMA` | Terima | Order diterima dari customer |
| 2 | `PILAH` | Pilah | Pakaian dipilah berdasarkan jenis dan warna |
| 3 | `CUCI` | Cuci | Proses pencucian |
| 4 | `KERING` | Kering | Proses pengeringan |
| 5 | `LIPAT` | Lipat | Pakaian dilipat dan di-packing |
| 6 | `CEK` | Cek | Pengecekan kualitas akhir |
| 7 | `SIAP` | Siap | Siap diambil customer |
| 8 | `DIAMBIL` | Diambil | Sudah diambil customer |

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

All routes use the `admin.*` name prefix, nested under `auth` → `verified` → `branch` middleware.

```php
Route::middleware(['auth', 'verified'])->name('admin.')->group(function () {
    Route::middleware(['branch'])->group(function () {
        Route::get('/workshop', [WorkshopController::class, 'index'])
            ->middleware('permission:workshop.read')->name('workshop.index');
        Route::get('/workshop/scan', [WorkshopController::class, 'scan'])
            ->middleware('permission:workshop.scan')->name('workshop.scan');
        Route::post('/workshop/scan', [WorkshopController::class, 'lookup'])
            ->middleware('permission:workshop.scan')->name('workshop.lookup');
        Route::get('/workshop/orders/{order}', [WorkshopController::class, 'orderDetail'])
            ->middleware('permission:workshop.read')->name('workshop.order-detail');
        Route::post('/workshop/items/{orderItem}/status', [WorkshopController::class, 'updateStatus'])
            ->middleware('permission:workshop.update_status|quality_check')->name('workshop.update-status');
        Route::get('/workshop/items/{orderItem}', [WorkshopController::class, 'show'])
            ->middleware('permission:workshop.read')->name('workshop.items.show');
    });
});
```

**Route names reference:**
| Action | Name | Permission |
|--------|------|-----------|
| Workshop index | `admin.workshop.index` | `workshop.read` |
| Scan page | `admin.workshop.scan` | `workshop.scan` |
| Lookup by code | `admin.workshop.lookup` | `workshop.scan` |
| Order detail | `admin.workshop.order-detail` | `workshop.read` |
| Update item status | `admin.workshop.update-status` | `workshop.update_status\|quality_check` |
| Item show | `admin.workshop.items.show` | `workshop.read` |

**Dedicated scanner route** (separate from workshop scan):
| Action | Name | Permission |
|--------|------|-----------|
| Scanner index | `admin.scanner.index` | `workshop.scan` |
| Scanner lookup | `admin.scanner.lookup` | `workshop.scan` |

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
