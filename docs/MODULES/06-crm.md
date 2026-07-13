# Module 06: CRM

## Overview

Customer management, membership tiers, and loyalty points system.

## Tables

- `customers` — Customer data with membership info
- `membership_tiers` — Bronze/Silver/Gold/Platinum
- `loyalty_points_transactions` — Points earn/redeem/expire history

## Customer Management

### Fields
- Code (auto: `CUS-00001`), name, phone, email, address
- KTP number, birth date, gender
- Membership info: tier, total points, total purchase, total orders
- Notes, active status

### Registration
- **Admin/CS only** — no public registration
- CS creates customer via CRM form
- Walk-in orders can create customer on the fly

## Membership Tiers

| Tier | Level | Min Points | Discount | Benefits |
|------|-------|-----------|----------|----------|
| Bronze | 1 | 0 | 0% | — |
| Silver | 2 | 500 | 5% | Diskon 5% |
| Gold | 3 | 1,500 | 10% | Diskon 10% + gratis antar |
| Platinum | 4 | 5,000 | 15% | Diskon 15% + gratis antar + prioritas + voucher ulang tahun Rp 50k |

### Tier Management
- Owner/Super Admin can manage tiers via CRM → Membership Tiers
- Fields: name, min points, discount %, discount per order, free delivery, priority, birthday voucher, benefits

### Auto-Upgrade
```php
// Called when points change
public function checkMembershipUpgrade(Customer $customer): void
{
    $nextTier = MembershipTier::where('min_points', '<=', $customer->total_points)
        ->where('level', '>', $customer->tier?->level ?? 0)
        ->orderBy('level', 'desc')
        ->first();
    
    if ($nextTier && $nextTier->id !== $customer->membership_tier_id) {
        $customer->update(['membership_tier_id' => $nextTier->id]);
        event(new CustomerMembershipUpgraded($customer, $oldTier, $nextTier->name));
    }
}
```

## Loyalty Points

### Rules (configurable via Settings)
| Setting | Default | Key |
|---------|---------|-----|
| Points ratio | Rp 1.000 = 1 poin | `loyalty.points_ratio` |
| Redeem rate | 100 poin = Rp 1.000 | `loyalty.points_redeem_rate` |
| Expiry | 90 days | `loyalty.points_expiry_days` |
| Min order for points | Rp 0 | `loyalty.min_order_amount` |
| Auto upgrade | Yes | `loyalty.auto_upgrade` |

### Transaction Types
| Type | Signs | Trigger |
|------|-------|---------|
| `earn` | +points | Order paid (grand_total / ratio) |
| `redeem` | -points | Customer uses points for discount |
| `expire` | -points | Cron job: points older than expiry days |
| `adjust` | +/-points | Manual adjustment by admin |

### Points in POS
```
Selebelum pembayaran, CS/Cashier can offer:
"Pakai poin? Tersedia 1.240 poin (setara Rp 12.400)"
Customer chooses → points deducted → discount applied
```

## Customer Detail UI

```
CRM → CUSTOMERS → Detail
┌─────────────────────────────────────────────────────┐
│  Bpk. Amir                         [Edit] [WA] [Hps]│
├─────────────────────────────────────────────────────┤
│  Tabs: [Info] [Orders] [Poin] [Notes]               │
├─────────────────────────────────────────────────────┤
│  INFO:                                               │
│  No. HP   : 0812-xxxx-xxxx                          │
│  Email    : amir@email.com                          │
│  Alamat   : Jl. Merdeka No. 45                      │
│  KTP      : 3174xxxxxxxx                            │
│  Bergabung: 01 Jan 2026                             │
│                                                     │
│  Membership: 🥇 GOLD                                │
│  Poin      : 1.240 poin (Rp 12.400)                 │
│  Total     : 12 order | Rp 1.240.000                │
│                                                     │
│  ─── TAB: ORDERS ───                                │
│  #       │ Tgl      │ Total   │ Status             │
│  B001... │ 08 Jul   │ 44.400  │ ✅ Ambil            │
│                                                     │
│  ─── TAB: POIN ───                                  │
│  Tgl     │ Tipe   │ Poin  │ Saldo  │ Ket            │
│  08 Jul  │ Earn   │ +40   │ 1.240  │ Order B001      │
│  01 Jul  │ Redeem │ -100  │ 1.200  │ Diskon Rp 10rb  │
└─────────────────────────────────────────────────────┘
```

## Files

```
app/Models/Customer.php
app/Models/MembershipTier.php
app/Models/LoyaltyPointsTransaction.php
app/Services/Customer/CustomerService.php
app/Services/Customer/LoyaltyPointsService.php
app/Services/Customer/MembershipService.php
app/Http/Controllers/Web/CustomerController.php
app/Http/Controllers/Web/MembershipTierController.php
app/Console/Commands/ExpireLoyaltyPoints.php
database/migrations/create_customers_table.php
database/migrations/create_membership_tiers_table.php
database/migrations/create_loyalty_points_transactions_table.php
database/seeders/MembershipTierSeeder.php
resources/views/customers/index.blade.php
resources/views/customers/create.blade.php
resources/views/customers/edit.blade.php
resources/views/customers/show.blade.php
resources/views/membership-tiers/index.blade.php
resources/views/membership-tiers/edit.blade.php
```
