# Customer Tracking Public Page — UI/UX Design

## Overview

Public-facing order tracking page (no login required). Mobile-first, accessible via unique link sent through WhatsApp. Used by customers to check their laundry status in real-time.

## URL Structure

```
https://domain.com/track/{token}
Example: https://domain.com/track/550e8400-e29b-41d4-a716-446655440000
```

## Page Flow

```
1. Customer clicks link from WA
         ↓
2. PIN verification screen (last 2 digits of phone)
         ↓
3. Order detail with timeline
         ↓
4. Optional: Contact via WhatsApp
```

## Screen 1: PIN Verification

```
┌──────────────────────────────────────────────┐
│                                              │
│          🏠 ISTANA LAUNDRY                    │
│                                              │
│   ┌──────────────────────────────┐           │
│   │                              │           │
│   │   🔒 Masukkan PIN            │           │
│   │                              │           │
│   │   PIN dikirim ke WhatsApp    │           │
│   │   Anda saat pemesanan        │           │
│   │                              │           │
│   │   [●] [●] [●] [●]           │           │
│   │         (4 digit)            │           │
│   │                              │           │
│   │   [Verifikasi]               │           │
│   │                              │           │
│   │   Tidak terima PIN?          │           │
│   │   [Hubungi Kami]             │           │
│   │                              │           │
│   └──────────────────────────────┘           │
│                                              │
└──────────────────────────────────────────────┘
```

### PIN Rules
- PIN = last 2 or 4 digits of registered phone number
- No retry limit displayed (but backend has rate limit)
- If wrong: "PIN salah. Silakan coba lagi."
- If 5 failed attempts: "Terlalu banyak percobaan. Hubungi kami untuk bantuan."

## Screen 2: Order Tracking (Verified)

```
┌──────────────────────────────────────────────┐
│                                              │
│   ← Kembali                                   │
│                                              │
│   ┌──────────────────────────────┐           │
│   │   ISTANA LAUNDRY            │           │
│   │   Status Pesanan Anda        │           │
│   └──────────────────────────────┘           │
│                                              │
│   Order: CAB-20260709-00001                  │
│   Tanggal: 09 Juli 2026                      │
│                                              │
│   ═══════════════════════════════             │
│                                              │
│   STATUS SAAT INI                            │
│   ┌────────────────────────────────┐         │
│   │                                │         │
│   │   🌀 DICUCI                    │         │
│   │   Status ke-3 dari 8           │         │
│   │   (Perkiraan selesai: 14:30)   │         │
│   │                                │         │
│   └────────────────────────────────┘         │
│                                              │
│   ═══════════════════════════════             │
│                                              │
│   PROGRESS PENGERJAAN                        │
│                                              │
│   ● TERIMA      09:30 ✓                      │
│   ● PILAH       09:45 ✓                      │
│   ● CUCI        10:15 ◉ <-- (current)        │
│   ○ KERING                                   │
│   ○ LIPAT                                    │
│   ○ CEK                                      │
│   ○ SIAP                                     │
│   ○ DIAMBIL                                  │
│                                              │
│   ═══════════════════════════════             │
│                                              │
│   ITEM YANG DIKERJAKAN                       │
│                                              │
│   ┌──────────────────┬──────┬────────┐       │
│   │ Layanan          │ Qty  │ Status │       │
│   ├──────────────────┼──────┼────────┤       │
│   │ Cuci Kering      │ 3 kg │ DICUCI │       │
│   │ Setrika          │ 2 kg │ KERING │       │
│   └──────────────────┴──────┴────────┘       │
│                                              │
│   ═══════════════════════════════             │
│                                              │
│   TOTAL PEMBAYARAN                           │
│   Rp 21.000                                  │
│   Status: ✅ LUNAS                           │
│                                              │
│   ═══════════════════════════════             │
│                                              │
│   BUTUH BANTUAN?                             │
│   [📱 Hubungi via WhatsApp]                  │
│                                              │
│   🏪 Istana Laundry Samarinda                │
│   Jl. Hidayatullah, Samarinda                │
│                                              │
└──────────────────────────────────────────────┘
```

## Status Visual Indicators

| Status | Icon | Color | Dot |
|--------|------|-------|-----|
| Completed | ✓ | `#10B981` (green) | `●` |
| Current | ◉ | `#FF6B00` (orange) | `●` |
| Pending | ○ | `#E5E5E5` (gray) | `○` |

## Motion & Animation (from landing page)

```css
/* Entry animation for verified state */
@keyframes snapUp {
    0% { transform: translateY(36px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}

/* Pulse for active status */
@keyframes pulse-dot {
    0%, 100% { box-shadow: 0 0 0 0 rgba(255,107,0,.4); }
    50% { box-shadow: 0 0 0 0 rgba(255,107,0,0); }
}
```

## Responsive Breakpoints

- **Mobile (default)**: Single column, full-width cards
- **Tablet (md:)**: Slightly larger padding, wider timelines
- **Desktop (lg:)**: Max-width container, never wider than 640px for the tracking card

## Files

```
resources/views/tracking/show.blade.php               # Main page with PIN/tracking states
resources/views/tracking/partials/pin-form.blade.php  # PIN verification form
resources/views/tracking/partials/timeline.blade.php  # Status timeline
resources/views/tracking/partials/items.blade.php     # Items table
resources/views/tracking/partials/contact.blade.php   # Contact section
resources/views/tracking/css/tracking.css             # Public page CSS (minimal)
resources/views/tracking/js/tracking.js               # Alpine.js for public page
```
