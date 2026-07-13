# Changelog

All notable changes to the Istana Laundry System will be documented in this file.

## [Unreleased]

### Added
- Initial project scaffolding (Laravel 11 + Breeze + Tailwind CSS v4 + Alpine.js)
- Authentication system with 8 user roles (Developer, Owner, Super Admin, Branch Admin, Workshop Admin, CS, Cashier, Workshop Staff)
- Role-based access control via Spatie Permission
- Branch management with multi-workshop support
- Multi-level user hierarchy (Developer > Owner > Super Admin > Branch Admin)
- Branch context middleware (`SetBranchContext`) and `HasBranchScope` trait
- Master data: Services (CK, CB, ST, CK+ST, EXP, SL, LP, KP, SF) and branch-specific pricing
- POS module: Order number generation, 5-status lifecycle, payment methods (cash/transfer/QRIS/gateway)
- Workshop module: QR scan per item, 8 production statuses, forward-only rule, WA notification modal
- CRM module: Customer management, membership tiers (Bronze/Silver/Gold/Platinum), loyalty points
- Promotions engine: Percentage, fixed, and buy-x-get-y types with per-branch toggle
- Finance module: Double-entry accounting, auto-posting, COA with tax accounts, PP 23 (0.5%) and PKP (PPN 11%)
- Inventory module: FIFO full with batch tracking, COGS auto-journal
- Dashboard & reports per role (5 tabs: Pendapatan, Operasional, Produksi, Keuangan, Inventory)
- Customer tracking public page (`/track/{token}`) with PIN verification
- Settings engine: 9 groups (General, Branch, Tax, Loyalty, Gateway, Accounting, Order, Notification, Inventory)
- Midtrans payment gateway integration
- Audit trail (database + file), export Excel/PDF, Spatie Backup (manual trigger)
- Landing page design reference from `istanalaundry.alk-tech.my.id` (black + #FF6B00 orange)
- Admin layout adapted from TailAdmin Pro (sidebar + header + content grid)
