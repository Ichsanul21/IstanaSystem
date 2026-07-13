<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuditController;
use App\Http\Controllers\Web\BackupController;
use App\Http\Controllers\Web\BranchController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\MembershipTierController;
use App\Http\Controllers\Web\DailyCashFlowController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\FinanceController;
use App\Http\Controllers\Web\InventoryItemController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\PromotionController;
use App\Http\Controllers\Web\RefundController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\ServiceController;
use App\Http\Controllers\Web\ServicePricingController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\TrackingController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\WorkshopController;
use App\Http\Controllers\Web\ChartOfAccountController;
use App\Http\Controllers\Web\AccountingPeriodController;
use App\Http\Controllers\Web\ExpenseController;
use App\Http\Controllers\Web\GatewayConfigurationController;
use App\Http\Controllers\Web\POSController;
use App\Http\Controllers\Web\ScannerController;
use App\Http\Controllers\Web\ExportController;
use App\Http\Controllers\Web\InventoryStockController;
use App\Http\Controllers\Web\ActivityLogController;

// Public tracking (no auth)
Route::get('/track/{token}', [TrackingController::class, 'show'])->name('tracking.show');
Route::post('/track/{token}/verify', [TrackingController::class, 'verify'])
    ->middleware('throttle:5,15')
    ->name('tracking.verify');

// Auth routes (Breeze already defines these, so we add our admin routes after)

Route::middleware(['auth', 'verified'])->name('admin.')->group(function () {

    // Branch context middleware group
    Route::middleware(['branch'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [DashboardController::class, 'index'])->name('home');

        // Branch switcher
        Route::post('/branch/switch/{branch}', [BranchController::class, 'switch'])->name('branch.switch');

        // Branches
        Route::resource('branches', BranchController::class)->middleware('role:Developer,Super Admin');

        // Users
        Route::resource('users', UserController::class)->middleware('role:Developer,Super Admin,Branch Admin');

        // Customers
        Route::resource('customers', CustomerController::class);
        Route::get('/customers/by-phone/{phone}', [CustomerController::class, 'getByPhone'])->name('customers.by-phone');
        Route::post('/customers/{customer}/points', [CustomerController::class, 'addPoints'])->name('customers.points');
        Route::post('/customers/{customer}/notes', [CustomerController::class, 'addNote'])->name('customers.notes');
        Route::get('/customers/search/json', [CustomerController::class, 'search'])->name('customers.search-api');
        Route::post('/customers/quick-create', [CustomerController::class, 'quickStore'])->name('customers.quick-store');

        // POS
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');

        // Orders
        Route::resource('orders', OrderController::class);
        Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');

        // Payments
        Route::get('/orders/{order}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/orders/{order}/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

        // Refunds
        Route::get('/refunds', [RefundController::class, 'index'])->name('refunds.index');
        Route::post('/orders/{order}/refunds', [RefundController::class, 'store'])->name('refunds.store');
        Route::post('/refunds/{refund}/approve', [RefundController::class, 'approve'])->name('refunds.approve');
        Route::post('/refunds/{refund}/complete', [RefundController::class, 'complete'])->name('refunds.complete');
        Route::post('/refunds/{refund}/reject', [RefundController::class, 'reject'])->name('refunds.reject');

        // Workshop
        Route::get('/workshop', [WorkshopController::class, 'index'])->name('workshop.index');
        Route::get('/workshop/scan', [WorkshopController::class, 'scan'])->name('workshop.scan');
        Route::post('/workshop/scan', [WorkshopController::class, 'lookup'])->name('workshop.lookup');
        Route::get('/workshop/orders/{order}', [WorkshopController::class, 'orderDetail'])->name('workshop.order-detail');
        Route::post('/workshop/items/{orderItem}/status', [WorkshopController::class, 'updateStatus'])->name('workshop.update-status');
        Route::get('/workshop/items/{orderItem}', [WorkshopController::class, 'show'])->name('workshop.items.show');

        // Promotions
        Route::resource('promotions', PromotionController::class);
        Route::post('/promotions/{promotion}/branches/{branch}/toggle', [PromotionController::class, 'toggleBranch'])->name('promotions.toggle-branch');
        Route::get('/promotions/check/{code}', [PromotionController::class, 'check'])->name('promotions.check');

        // Inventory
        Route::resource('inventory', InventoryItemController::class);
        Route::post('/inventory/{item}/add-stock', [InventoryItemController::class, 'addStock'])->name('inventory.add-stock');
        Route::post('/inventory/{item}/transfer', [InventoryItemController::class, 'transfer'])->name('inventory.transfer');
        Route::get('/inventory/{item}/stock-out', [InventoryStockController::class, 'out'])->name('inventory.stock.out');
        Route::post('/inventory/{item}/stock-out', [InventoryStockController::class, 'deduct'])->name('inventory.stock.deduct');

        // Services
        Route::resource('services', ServiceController::class);

        // Service Pricings
        Route::prefix('services/pricings')->name('services.pricings.')->group(function () {
            Route::get('/', [ServicePricingController::class, 'index'])->name('index');
            Route::get('/create', [ServicePricingController::class, 'create'])->name('create');
            Route::post('/', [ServicePricingController::class, 'store'])->name('store');
            Route::get('/{pricing}/edit', [ServicePricingController::class, 'edit'])->name('edit');
            Route::put('/{pricing}', [ServicePricingController::class, 'update'])->name('update');
            Route::delete('/{pricing}', [ServicePricingController::class, 'destroy'])->name('destroy');
        });

        // Finance
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/', [FinanceController::class, 'index'])->name('index');
            Route::get('/accounts', [FinanceController::class, 'accounts'])->name('accounts');
            Route::get('/journal', [FinanceController::class, 'journal'])->name('journal');
            Route::get('/journal/create', [FinanceController::class, 'createJournal'])->name('journal.create');
            Route::post('/journal', [FinanceController::class, 'storeJournal'])->name('journal.store');
            Route::get('/trial-balance', [FinanceController::class, 'trialBalance'])->name('trial-balance');
            Route::get('/income-statement', [FinanceController::class, 'incomeStatement'])->name('income-statement');

            // Chart of Accounts CRUD
            Route::get('/coa/create', [ChartOfAccountController::class, 'create'])->name('coa.create');
            Route::post('/coa', [ChartOfAccountController::class, 'store'])->name('coa.store');
            Route::get('/coa/{chartOfAccount}/edit', [ChartOfAccountController::class, 'edit'])->name('coa.edit');
            Route::put('/coa/{chartOfAccount}', [ChartOfAccountController::class, 'update'])->name('coa.update');
            Route::delete('/coa/{chartOfAccount}', [ChartOfAccountController::class, 'destroy'])->name('coa.destroy');

            // Accounting Periods
            Route::get('/periods', [AccountingPeriodController::class, 'index'])->name('periods.index');
            Route::get('/periods/create', [AccountingPeriodController::class, 'create'])->name('periods.create');
            Route::post('/periods', [AccountingPeriodController::class, 'store'])->name('periods.store');
            Route::get('/periods/{accountingPeriod}/edit', [AccountingPeriodController::class, 'edit'])->name('periods.edit');
            Route::put('/periods/{accountingPeriod}', [AccountingPeriodController::class, 'update'])->name('periods.update');
            Route::delete('/periods/{accountingPeriod}', [AccountingPeriodController::class, 'destroy'])->name('periods.destroy');
            Route::post('/periods/{accountingPeriod}/close', [AccountingPeriodController::class, 'close'])->name('periods.close');

            // Expenses
            Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
            Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
            Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
            Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
            Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
            Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
        });

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('/settings/{group}', [SettingsController::class, 'group'])->name('settings.group');
        Route::post('/settings/{group}', [SettingsController::class, 'updateGroup'])->name('settings.group.update');

        // Branch Settings
        Route::get('/settings/branches/{branch}', [\App\Http\Controllers\Web\BranchSettingController::class, 'index'])->name('branch-settings.index');
        Route::post('/settings/branches/{branch}', [\App\Http\Controllers\Web\BranchSettingController::class, 'update'])->name('branch-settings.update');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
            Route::get('/orders', [ReportController::class, 'orders'])->name('orders');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/tax', [ReportController::class, 'tax'])->name('tax');
            Route::get('/production', [ReportController::class, 'production'])->name('production');
        });

        // Audit
        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
        Route::get('/audit/export', [ActivityLogController::class, 'export'])->name('audit.export');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        // Backup
        Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
        Route::post('/backup', [BackupController::class, 'create'])->name('backup.create');
        Route::get('/backup/{filename}/download', [BackupController::class, 'download'])->name('backup.download');
        Route::delete('/backup/{filename}', [BackupController::class, 'destroy'])->name('backup.destroy');

        // Daily Cash Flow
        Route::get('/cash-flow', [DailyCashFlowController::class, 'index'])->name('cash-flow.index');
        Route::post('/cash-flow', [DailyCashFlowController::class, 'store'])->name('cash-flow.store');

        // Scanner
        Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner.index');
        Route::post('/scanner/lookup', [ScannerController::class, 'lookup'])->name('scanner.lookup');

        // Gateway Configuration
        Route::get('/settings/gateway', [GatewayConfigurationController::class, 'index'])->name('settings.gateway');
        Route::post('/settings/gateway', [GatewayConfigurationController::class, 'update'])->name('settings.gateway.update');

        // Exports
        Route::prefix('exports')->name('exports.')->group(function () {
            Route::get('/revenue', [ExportController::class, 'revenue'])->name('revenue');
            Route::get('/orders', [ExportController::class, 'orders'])->name('orders');
            Route::get('/customers', [ExportController::class, 'customers'])->name('customers');
            Route::get('/inventory', [ExportController::class, 'inventory'])->name('inventory');
        });

        // Membership Tiers
        Route::resource('membership-tiers', MembershipTierController::class)->only(['index', 'edit', 'update'])->middleware('role:Developer,Super Admin');
    });
});
