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
Route::post('/track/{token}/verify', [TrackingController::class, 'verifyPin'])
    ->middleware('throttle:5,15')
    ->name('tracking.verify');

// Authenticated root redirect
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('home');

// Auth routes (Breeze already defines these, so we add our admin routes after)

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // ===== ADMIN (NO BRANCH) — system-wide routes =====

    // Settings (global)
    Route::get('/settings', [SettingsController::class, 'index'])->middleware('permission:settings.read|settings.update|edit_global_settings')->name('settings.index');
    Route::get('/settings/{group}', [SettingsController::class, 'group'])->middleware('permission:settings.read')->name('settings.group');
    Route::post('/settings/{group}', [SettingsController::class, 'updateGroup'])->middleware('permission:settings.update')->name('settings.group.update');

    // Gateway Configuration (global)
    Route::get('/settings/gateway', [GatewayConfigurationController::class, 'index'])->middleware('permission:manage_gateway_config')->name('settings.gateway');
    Route::post('/settings/gateway', [GatewayConfigurationController::class, 'update'])->middleware('permission:manage_gateway_config')->name('settings.gateway.update');

    // Audit & Activity Logs
    Route::get('/audit', [AuditController::class, 'index'])->middleware('permission:view_activity_logs')->name('audit.index');
    Route::get('/audit/export', [ActivityLogController::class, 'index'])->middleware('permission:view_activity_logs')->name('audit.export');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->middleware('permission:view_activity_logs')->name('activity-logs.index');

    // Backup
    Route::get('/backup', [BackupController::class, 'index'])->middleware('permission:run_backup|view_system_info')->name('backup.index');
    Route::post('/backup', [BackupController::class, 'create'])->middleware('permission:run_backup')->name('backup.create');
    Route::get('/backup/{filename}/download', [BackupController::class, 'download'])->middleware('permission:run_backup')->name('backup.download');
    Route::delete('/backup/{filename}', [BackupController::class, 'destroy'])->middleware('permission:run_backup')->name('backup.destroy');

    // ===== ADMIN (BRANCH-SCOPED) =====
    Route::middleware(['branch'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [DashboardController::class, 'index'])->name('home');

        // Branch switcher
        Route::post('/branch/switch/{branch}', [BranchController::class, 'switch'])
            ->middleware('permission:switch_branch')->name('branch.switch');

        // Branches
        Route::resource('branches', BranchController::class)->middleware('permission:branch.create|branch.read|branch.update|branch.delete');

        // Users
        Route::resource('users', UserController::class)->middleware('permission:user.read|user.create|user.update|user.delete');

        // Customers
        Route::resource('customers', CustomerController::class)->middleware('permission:customer.read|customer.create|customer.update|customer.delete');
        Route::get('/customers/by-phone/{phone}', [CustomerController::class, 'getByPhone'])->name('customers.by-phone');
        Route::post('/customers/{customer}/points', [CustomerController::class, 'addPoints'])->name('customers.points');
        Route::post('/customers/{customer}/notes', [CustomerController::class, 'addNote'])->middleware('permission:customer.update')->name('customers.notes');
        Route::get('/customers/search/json', [CustomerController::class, 'search'])->name('customers.search-api');
        Route::post('/customers/quick-create', [CustomerController::class, 'quickStore'])->middleware('permission:customer.create')->name('customers.quick-store');

        // POS
        Route::get('/pos', [POSController::class, 'index'])->middleware('permission:order.create')->name('pos.index');

        // Orders
        Route::resource('orders', OrderController::class)->middleware('permission:order.read|order.create|order.update|order.delete');
        Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->middleware('permission:order.read')->name('orders.receipt');

        // Payments
        Route::get('/orders/{order}/payments/create', [PaymentController::class, 'create'])->middleware('permission:payment.create')->name('orders.payments.create');
        Route::post('/orders/{order}/payments', [PaymentController::class, 'store'])->middleware('permission:payment.create')->name('orders.payments.store');
        Route::get('/orders/{order}/payments/{payment}', [PaymentController::class, 'show'])->middleware('permission:payment.read')->name('orders.payments.show');

        // Refunds
        Route::get('/refunds', [RefundController::class, 'index'])->middleware('permission:process_refund|approve_refund')->name('refunds.index');
        Route::post('/orders/{order}/refunds', [RefundController::class, 'store'])->middleware('permission:process_refund')->name('refunds.store');
        Route::post('/refunds/{refund}/approve', [RefundController::class, 'approve'])->middleware('permission:approve_refund')->name('refunds.approve');
        Route::post('/refunds/{refund}/complete', [RefundController::class, 'complete'])->middleware('permission:process_refund')->name('refunds.complete');
        Route::post('/refunds/{refund}/reject', [RefundController::class, 'reject'])->middleware('permission:approve_refund')->name('refunds.reject');

        // Workshop
        Route::get('/workshop', [WorkshopController::class, 'index'])->middleware('permission:workshop.read')->name('workshop.index');
        Route::get('/workshop/scan', [WorkshopController::class, 'scan'])->middleware('permission:workshop.scan')->name('workshop.scan');
        Route::post('/workshop/scan', [WorkshopController::class, 'lookup'])->middleware('permission:workshop.scan')->name('workshop.lookup');
        Route::get('/workshop/orders/{order}', [WorkshopController::class, 'orderDetail'])->middleware('permission:workshop.read')->name('workshop.order-detail');
        Route::post('/workshop/items/{orderItem}/status', [WorkshopController::class, 'updateStatus'])->middleware('permission:workshop.update_status|quality_check')->name('workshop.update-status');
        Route::get('/workshop/items/{orderItem}', [WorkshopController::class, 'show'])->middleware('permission:workshop.read')->name('workshop.items.show');

        // Promotions
        Route::resource('promotions', PromotionController::class)->middleware('permission:promotion.read|promotion.create|promotion.update|promotion.delete');
        Route::post('/promotions/{promotion}/branches/{branch}/toggle', [PromotionController::class, 'toggleBranch'])->middleware('permission:toggle_promotion_branch')->name('promotions.toggle-branch');
        Route::get('/promotions/check/{code}', [PromotionController::class, 'check'])->middleware('permission:promotion.read')->name('promotions.check');

        // Inventory
        Route::resource('inventory', InventoryItemController::class)->middleware('permission:inventory.read|inventory.create|inventory.update|inventory.delete');
        Route::post('/inventory/{item}/add-stock', [InventoryItemController::class, 'addStock'])->middleware('permission:stock_in')->name('inventory.add-stock');
        Route::post('/inventory/{item}/transfer', [InventoryItemController::class, 'transfer'])->middleware('permission:stock_out')->name('inventory.transfer');
        Route::get('/inventory/{item}/stock-out', [InventoryStockController::class, 'out'])->middleware('permission:stock_out')->name('inventory.stock.out');
        Route::post('/inventory/{item}/stock-out', [InventoryStockController::class, 'deduct'])->middleware('permission:stock_out')->name('inventory.stock.deduct');
        Route::get('/inventory/stock', [InventoryStockController::class, 'index'])->middleware('permission:inventory.read')->name('inventory.stock.index');
        Route::get('/inventory/stock/create', [InventoryStockController::class, 'create'])->middleware('permission:stock_in')->name('inventory.stock.create');
        Route::post('/inventory/stock', [InventoryStockController::class, 'store'])->middleware('permission:stock_in')->name('inventory.stock.store');

        // Services
        Route::resource('services', ServiceController::class)->middleware('permission:view_services|create_services|edit_services');

        // Service Pricings
        Route::prefix('services/pricing')->name('services.pricing.')->middleware('permission:edit_service_pricing')->group(function () {
            Route::get('/', [ServicePricingController::class, 'index'])->name('index');
            Route::get('/create', [ServicePricingController::class, 'create'])->name('create');
            Route::post('/', [ServicePricingController::class, 'store'])->name('store');
            Route::get('/{pricing}/edit', [ServicePricingController::class, 'edit'])->name('edit');
            Route::put('/{pricing}', [ServicePricingController::class, 'update'])->name('update');
            Route::delete('/{pricing}', [ServicePricingController::class, 'destroy'])->name('destroy');
        });

        // Finance
        Route::prefix('finance')->name('finance.')->middleware('permission:finance.read|create_manual_journal|manage_accounting_periods|manage_expenses')->group(function () {
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

        // Branch Settings
        Route::get('/settings/branches/{branch}', [\App\Http\Controllers\Web\BranchSettingController::class, 'index'])->middleware('permission:edit_branch_settings')->name('branch-settings.index');
        Route::post('/settings/branches/{branch}', [\App\Http\Controllers\Web\BranchSettingController::class, 'update'])->middleware('permission:edit_branch_settings')->name('branch-settings.update');

        // Reports
        Route::prefix('reports')->name('reports.')->middleware('permission:report.read|view_financial_reports')->group(function () {
            Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
            Route::get('/orders', [ReportController::class, 'orders'])->name('orders');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/tax', [ReportController::class, 'tax'])->name('tax');
            Route::get('/production', [ReportController::class, 'production'])->name('production');
        });

        // Daily Cash Flow
        Route::get('/cash-flow', [DailyCashFlowController::class, 'index'])->middleware('permission:finance.read|create_manual_journal')->name('cash-flow.index');
        Route::post('/cash-flow', [DailyCashFlowController::class, 'store'])->middleware('permission:create_manual_journal')->name('cash-flow.store');

        // Scanner
        Route::get('/scanner', [ScannerController::class, 'index'])->middleware('permission:workshop.scan')->name('scanner.index');
        Route::post('/scanner/lookup', [ScannerController::class, 'lookup'])->middleware('permission:workshop.scan')->name('scanner.lookup');

        // Exports
        Route::prefix('exports')->name('exports.')->middleware('permission:export_data')->group(function () {
            Route::get('/revenue', [ExportController::class, 'revenueExcel'])->name('revenue');
            Route::get('/orders', [ExportController::class, 'ordersExcel'])->name('orders');
            Route::get('/customers', [ExportController::class, 'customersExcel'])->name('customers');
            Route::get('/inventory', [ExportController::class, 'inventoryExcel'])->name('inventory');
            Route::get('/tax', [ExportController::class, 'taxExcel'])->name('tax');
            Route::get('/production', [ExportController::class, 'productionExcel'])->name('production');
            Route::get('/journal', [ExportController::class, 'journalExcel'])->name('journal');
        });

        // Membership Tiers
        Route::resource('membership-tiers', MembershipTierController::class)->only(['index', 'edit', 'update'])->middleware('permission:manage_tiers');
    });
});
