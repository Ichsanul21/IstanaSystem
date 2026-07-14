# API Changelog

## v1.1.0 (2026-07-14)
- Added: Rate limiting (60/30/120 req/min for API/tracking/webhook)
- Added: X-Branch-Id header support via SetBranchFromHeader middleware
- Changed: All API responses now use consistent `{success, data, message}` format via ApiResponse helper
- Changed: Tracking endpoint `GET /track/{token}` now returns order under `data` key with timeline, items, branch info
- Changed: `POST /track/{token}/verify` returns `{"verified": true}` on success (not full order)
- Changed: Wrong PIN on tracking verify now returns HTTP 422 (was 403)
- Changed: `GET /promotions/eligible/{orderId}` now includes `estimated_discount` field
- Changed: `GET /workshop/scan` now includes `id` in current_status and next_status
- Added: `GET /dashboard/operational` endpoint for operational metrics
- Added: Settings validation per group (general, tax, loyalty, accounting, order, notification, inventory, gateway)
- Added: Sanctum token authentication (auth:sanctum) on all API CRUD routes
- Fixed: Webhook response now returns `{"ok": true}` instead of `{"message": "OK"}`

## v1.0.0 (2026-07-01)
- Initial API release
- Auth (login, register, logout)
- Branches, Master Data (services, service pricings)
- Orders (CRUD, payment, refund, receipt)
- Workshop (scan, update, queue, stats)
- Customers (CRUD, search)
- Promotions (list, eligible, validate)
- Finance (summary, transactions, export)
- Inventory (CRUD, movements)
- Dashboard (summary, operational, charts)
- Settings (get, update by group)
- Payment Gateway (Midtrans, webhooks)
- Customer Tracking (public, PIN verify)
