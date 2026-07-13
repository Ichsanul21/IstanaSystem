# Module 03: Master Data

## Overview

Service types and branch-specific pricing.

## Tables

- `services` — Laundry service types
- `service_pricings` — Branch-specific pricing (UNIQUE: service_id, branch_id)

## Services (Default)

| Code | Name | Unit | Notes |
|------|------|------|-------|
| CK | Cuci Kering | kg | Basic wash & dry |
| CB | Cuci Basah | kg | Wet wash without drying |
| ST | Setrika | kg | Ironing only |
| CK+ST | Cuci Kering + Setrika | kg | Full service |
| EXP | Express | kg | Same-day service |
| SL | Selimut | pcs | Blankets / bedding |
| LP | Lipat | kg | Folding only |
| KP | Karpet | m² | Carpet cleaning (per sqm) |
| SF | Sofa | pcs | Sofa cleaning (per piece) |

## Service Pricing

```php
// Each branch has its own price for each service
service_pricings
├── service_id → services.id
├── branch_id → branches.id
├── price (e.g., Rp 5.000 for CK at Cabang A)
├── min_weight (e.g., 3 kg minimum)
└── is_active
```

### Pricing Rule
- When creating order, price is fetched from `service_pricings` by branch + service
- Branch Admin can update prices for their branch
- Super Admin/Developer can set default pricing and override per branch

## UI

```
MASTER DATA → Services
┌──────────────────────────────────────────────┐
│  Services                    [+ Tambah Service]│
├──────────────────────────────────────────────┤
│ [Cari...]  [All Units ▼]                     │
├──────┬─────────────┬──────┬────────┬─────────┤
│ Code │ Nama        │ Unit │ Status │ Aksi    │
├──────┼─────────────┼──────┼────────┼─────────┤
│ CK   │ Cuci Kering │ kg   │ ✓ Aktif│ [Edit]  │
│ CB   │ Cuci Basah  │ kg   │ ✓ Aktif│ [Edit]  │
└──────┴─────────────┴──────┴────────┴─────────┘

MASTER DATA → Pricing per Branch
┌──────────────────────────────────────────────┐
│  Harga — Cabang A              [+ Atur Harga] │
├──────────────────────────────────────────────┤
│ Service        │ Harga  │ Min    │ Aksi      │
├────────────────┼────────┼────────┼───────────┤
│ Cuci Kering    │ 5.000  │ 3 kg   │ [Edit]    │
│ Cuci Basah     │ 4.000  │ 3 kg   │ [Edit]    │
│ Setrika        │ 4.000  │ 3 kg   │ [Edit]    │
│ Cuci + Setrika │ 7.000  │ 3 kg   │ [Edit]    │
│ Karpet (m²)    │ 15.000 │ 1 m²   │ [Edit]    │
│ Sofa (pcs)     │ 50.000 │ 1 pcs  │ [Edit]    │
└────────────────┴────────┴────────┴───────────┘
```

## Files

```
app/Models/Service.php
app/Models/ServicePricing.php
app/Http/Controllers/Web/ServiceController.php
app/Http/Controllers/Web/ServicePricingController.php
database/migrations/create_services_table.php
database/migrations/create_service_pricings_table.php
database/seeders/ServiceSeeder.php
resources/views/services/index.blade.php
resources/views/services/create.blade.php
resources/views/services/edit.blade.php
resources/views/services/pricing.blade.php
```
