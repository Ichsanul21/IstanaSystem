# Testing Strategy

## Test Types

| Type | Tool | Scope |
|------|------|-------|
| Unit | PHPUnit | Models, Services, Helpers, Enums |
| Feature | PHPUnit | Controllers, API endpoints, Middleware |
| Browser | Laravel Dusk | UI flows, critical paths |

## Test Structure

```
tests/
├── Unit/
│   ├── Models/
│   │   ├── OrderTest.php
│   │   ├── CustomerTest.php
│   │   └── InventoryBatchTest.php
│   ├── Services/
│   │   ├── FifoServiceTest.php
│   │   ├── PromotionServiceTest.php
│   │   ├── LoyaltyPointsServiceTest.php
│   │   └── TaxServiceTest.php
│   └── Enums/
│       ├── OrderStatusTest.php
│       └── ProductionStatusTest.php
│
├── Feature/
│   ├── Auth/
│   │   ├── AuthenticationTest.php
│   │   ├── RolePermissionTest.php
│   │   └── BranchScopeTest.php
│   ├── POS/
│   │   ├── CreateOrderTest.php
│   │   ├── ProcessPaymentTest.php
│   │   └── RefundFlowTest.php
│   ├── Workshop/
│   │   ├── ScanQrTest.php
│   │   ├── StatusTransitionTest.php
│   │   └── InvalidTransitionTest.php
│   ├── CRM/
│   │   ├── CustomerCrudTest.php
│   │   ├── PointsEarnRedeemTest.php
│   │   └── MembershipUpgradeTest.php
│   ├── Finance/
│   │   ├── AutoJournalTest.php
│   │   ├── TaxCalculationTest.php
│   │   └── AccountingPeriodTest.php
│   ├── Inventory/
│   │   ├── FifoDeductionTest.php
│   │   ├── BatchTrackingTest.php
│   │   └── TransferTest.php
│   └── API/
│       ├── OrderApiTest.php
│       ├── CustomerApiTest.php
│       └── TrackingApiTest.php
│
└── Browser/
    ├── Pages/
    │   ├── LoginTest.php
    │   ├── PosOrderTest.php
    │   ├── WorkshopScanTest.php
    │   └── CustomerDetailTest.php
    └── Tracking/
        └── PublicTrackingPageTest.php
```

## What to Test

### Unit Tests (Business Logic)

| Test | Description |
|------|-------------|
| FIFO Deduction | Verify oldest batch is consumed first |
| FIFO Insufficient | Verify exception thrown when stock insufficient |
| Promo Calculator | Verify % / fixed / buy-x-get-y calculations |
| Points Earn | Verify correct points for order amount |
| Points Redeem | Verify discount = points / rate × 1000 |
| Membership Upgrade | Verify auto-upgrade at threshold |
| Tax PP23 | Verify 0.5% calculation |
| Tax PPN | Verify 11% calculation |
| Status Transition | Verify forward-only rule |
| Status Transition Invalid | Verify cannot skip or go back |
| Order Number Format | Verify `{CAB}-{YYYYMMDD}-{XXXXX}` |

### Feature Tests (Flow)

| Test | Description |
|------|-------------|
| Create Order | Full POS flow → items + promo + payment |
| Payment Methods | Cash/transfer/QRIS/gateway all work |
| Refund Flow | Request → Follow → Approve → Complete |
| Branch Scoping | Data isolated by branch |
| Role Authorization | Each role can only access their permissions |
| Customer CRUD | Create/edit/delete customer |
| Points Expire | Cron job expires old points correctly |
| Auto Journal | Payment creates correct journal entry |
| COGS Journal | Stock out creates COGS entry |

### Browser Tests (Dusk)

| Test | Description |
|------|-------------|
| Login Flow | Login → redirect to dashboard |
| POS Create Order | Full UI flow |
| Workshop Scan | QR scan → update status → WA modal |
| Customer Tracking Page | Open token → enter PIN → see status |

## Running Tests

```bash
# All tests
php artisan test

# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature

# Browser tests (Dusk)
php artisan dusk

# Coverage (if Xdebug/PCOV installed)
php artisan test --coverage
```

## CI/CD (Future)

```yaml
# .github/workflows/tests.yml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.4
        env:
          MYSQL_DATABASE: istana_test
          MYSQL_USER: test
          MYSQL_PASSWORD: test
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: 8.5 }
      - run: composer install
      - run: cp .env.testing .env
      - run: php artisan key:generate
      - run: php artisan migrate --env=testing
      - run: php artisan test
```
