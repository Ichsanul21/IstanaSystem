<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\BranchApiController;
use App\Http\Controllers\Api\V1\CustomerApiController;
use App\Http\Controllers\Api\V1\OrderApiController;
use App\Http\Controllers\Api\V1\DashboardApiController;
use App\Http\Controllers\Api\V1\PaymentApiController;
use App\Http\Controllers\Api\V1\ServiceApiController;
use App\Http\Controllers\Api\V1\TrackingApiController;
use App\Http\Controllers\Api\V1\FinanceApiController;
use App\Http\Controllers\Api\V1\InventoryApiController;
use App\Http\Controllers\Api\V1\SettingApiController;

// POS customer search (used by admin panel via AJAX, session-based auth)
Route::middleware(['auth:sanctum', 'auth.sync'])->get('/customers/search', [CustomerApiController::class, 'search']);

Route::prefix('v1')->group(function () {
    // Public
    Route::get('/track/{token}', [TrackingApiController::class, 'status'])->middleware(['branch.header', 'throttle:tracking']);
    Route::post('/track/{token}/verify', [TrackingApiController::class, 'verify'])->middleware(['branch.header', 'throttle:tracking']);

    // Auth (no sanctum required for login)
    Route::post('/auth/login', [AuthApiController::class, 'login']);

    // Authenticated
    Route::middleware(['auth:sanctum', 'auth.sync', 'branch.header', 'throttle:api'])->group(function () {
        // Auth
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);
        Route::get('/auth/me', [AuthApiController::class, 'me']);
        Route::put('/auth/profile', [AuthApiController::class, 'updateProfile']);

        // Branches
        Route::get('/branches', [BranchApiController::class, 'index'])->middleware('permission:branch.read');
        Route::get('/branches/{id}', [BranchApiController::class, 'show'])->middleware('permission:branch.read');
        Route::post('/branches', [BranchApiController::class, 'store'])->middleware('permission:branch.create');
        Route::put('/branches/{id}', [BranchApiController::class, 'update'])->middleware('permission:branch.update');
        Route::get('/branches/{id}/daily-cash-flow', [BranchApiController::class, 'dailyCashFlow'])->middleware('permission:finance.read');
        Route::get('/branches/switch/{id}', [BranchApiController::class, 'switchBranch'])->middleware('permission:switch_branch');
        Route::get('/workshops', [BranchApiController::class, 'workshops'])->middleware('permission:workshop.read');

        // Services
        Route::get('/services', [ServiceApiController::class, 'index'])->middleware('permission:view_services');
        Route::get('/services/{id}', [ServiceApiController::class, 'show'])->middleware('permission:view_services');
        Route::post('/services', [ServiceApiController::class, 'store'])->middleware('permission:create_services');
        Route::get('/service-pricings', [ServiceApiController::class, 'pricings'])->middleware('permission:view_services');
        Route::put('/service-pricings/{id}', [ServiceApiController::class, 'updatePricing'])->middleware('permission:edit_service_pricing');
        Route::put('/service-pricings/bulk', [ServiceApiController::class, 'bulkUpdatePricing'])->middleware('permission:edit_service_pricing');

        // Orders
        Route::apiResource('orders', OrderApiController::class)->middleware('permission:order.read|order.create|order.update|order.delete');
        Route::put('/orders/{order}/status', [OrderApiController::class, 'updateStatus'])->middleware('permission:order.update');
        Route::post('/orders/{id}/payment', [OrderApiController::class, 'payment'])->middleware('permission:payment.create');
        Route::post('/orders/{id}/refund', [OrderApiController::class, 'refund'])->middleware('permission:process_refund');
        Route::post('/orders/{id}/receipt', [OrderApiController::class, 'receipt'])->middleware('permission:order.read');
        Route::get('/orders/{id}/tracking-status', [OrderApiController::class, 'trackingStatus']);

        // Customers
        Route::apiResource('customers', CustomerApiController::class)->middleware('permission:customer.read|customer.create|customer.update|customer.delete');
        Route::get('/customers/{id}/orders', [CustomerApiController::class, 'orders'])->middleware('permission:customer.read');
        Route::get('/customers/{id}/points', [CustomerApiController::class, 'points'])->middleware('permission:customer.read');
        Route::post('/customers/{id}/adjust-points', [CustomerApiController::class, 'adjustPoints'])->middleware('permission:customer.update');
        Route::get('/membership-tiers', [CustomerApiController::class, 'membershipTiers'])->middleware('permission:manage_tiers');
        Route::get('/customers/lookup', [CustomerApiController::class, 'lookup']);

        // Workshop
        Route::get('/workshop/scan/{qrToken}', [\App\Http\Controllers\Api\V1\WorkshopApiController::class, 'scan'])->middleware('permission:workshop.scan');
        Route::post('/workshop/scan/{qrToken}/update', [\App\Http\Controllers\Api\V1\WorkshopApiController::class, 'updateScanStatus'])->middleware('permission:workshop.update_status');
        Route::get('/workshop/queue', [\App\Http\Controllers\Api\V1\WorkshopApiController::class, 'queue'])->middleware('permission:workshop.read');
        Route::get('/workshop/stats', [\App\Http\Controllers\Api\V1\WorkshopApiController::class, 'stats'])->middleware('permission:workshop.read');

        // Promotions
        Route::get('/promotions', [\App\Http\Controllers\Api\V1\PromotionApiController::class, 'index'])->middleware('permission:promotion.read');
        Route::get('/promotions/{id}', [\App\Http\Controllers\Api\V1\PromotionApiController::class, 'show'])->middleware('permission:promotion.read');
        Route::post('/promotions', [\App\Http\Controllers\Api\V1\PromotionApiController::class, 'store'])->middleware('permission:promotion.create');
        Route::put('/promotions/{id}', [\App\Http\Controllers\Api\V1\PromotionApiController::class, 'update'])->middleware('permission:promotion.update');
        Route::get('/promotions/eligible/{orderId}', [\App\Http\Controllers\Api\V1\PromotionApiController::class, 'eligible'])->middleware('permission:promotion.read');
        Route::post('/promotions/{id}/calculate', [\App\Http\Controllers\Api\V1\PromotionApiController::class, 'calculate'])->middleware('permission:promotion.read');

        // Dashboard
        Route::get('/dashboard/summary', [DashboardApiController::class, 'summary']);
        Route::get('/dashboard/revenue', [DashboardApiController::class, 'revenue']);
        Route::get('/dashboard/operational', [DashboardApiController::class, 'operational']);
        Route::get('/dashboard/production', [DashboardApiController::class, 'production']);
        Route::get('/dashboard/finance', [DashboardApiController::class, 'financeData']);
        Route::get('/dashboard/inventory', [DashboardApiController::class, 'inventoryData']);

        // Payments
        Route::post('/payments/midtrans/snap', [PaymentApiController::class, 'snapToken'])->middleware('permission:payment.create');
        Route::get('/payments/{orderId}/status', [PaymentApiController::class, 'status'])->middleware('permission:payment.read');
        Route::post('/payments/{orderId}/verify', [PaymentApiController::class, 'verify'])->middleware('permission:payment.read');

        // Finance
        Route::get('/finance/journal', [FinanceApiController::class, 'journalIndex'])->middleware('permission:finance.read');
        Route::get('/finance/journal/{id}', [FinanceApiController::class, 'journalShow'])->middleware('permission:finance.read');
        Route::post('/finance/journal', [FinanceApiController::class, 'journalStore'])->middleware('permission:create_manual_journal');
        Route::get('/finance/coa', [FinanceApiController::class, 'coaIndex'])->middleware('permission:finance.read');
        Route::get('/finance/coa/{id}', [FinanceApiController::class, 'coaShow'])->middleware('permission:finance.read');
        Route::get('/finance/coa/{id}/ledger', [FinanceApiController::class, 'coaLedger'])->middleware('permission:finance.read');
        Route::get('/finance/trial-balance', [FinanceApiController::class, 'trialBalance'])->middleware('permission:finance.read');
        Route::get('/finance/profit-loss', [FinanceApiController::class, 'profitLoss'])->middleware('permission:finance.read');
        Route::get('/finance/balance-sheet', [FinanceApiController::class, 'balanceSheet'])->middleware('permission:finance.read');
        Route::get('/finance/expenses', [FinanceApiController::class, 'expensesIndex'])->middleware('permission:manage_expenses');
        Route::post('/finance/expenses', [FinanceApiController::class, 'expensesStore'])->middleware('permission:manage_expenses');
        Route::get('/finance/tax/summary', [FinanceApiController::class, 'taxSummary'])->middleware('permission:manage_tax_config');
        Route::get('/finance/periods', [FinanceApiController::class, 'periodsIndex'])->middleware('permission:manage_accounting_periods');
        Route::post('/finance/periods', [FinanceApiController::class, 'periodsStore'])->middleware('permission:manage_accounting_periods');
        Route::post('/finance/periods/{id}/close', [FinanceApiController::class, 'periodsClose'])->middleware('permission:manage_accounting_periods');

        // Inventory
        Route::get('/inventory/items', [InventoryApiController::class, 'itemsIndex'])->middleware('permission:inventory.read');
        Route::get('/inventory/items/{id}', [InventoryApiController::class, 'itemsShow'])->middleware('permission:inventory.read');
        Route::post('/inventory/items', [InventoryApiController::class, 'itemsStore'])->middleware('permission:inventory.create');
        Route::put('/inventory/items/{id}', [InventoryApiController::class, 'itemsUpdate'])->middleware('permission:inventory.update');
        Route::get('/inventory/stock', [InventoryApiController::class, 'stockIndex'])->middleware('permission:inventory.read');
        Route::get('/inventory/stock/{itemId}/detail', [InventoryApiController::class, 'stockDetail'])->middleware('permission:inventory.read');
        Route::post('/inventory/stock/in', [InventoryApiController::class, 'stockIn'])->middleware('permission:stock_in');
        Route::post('/inventory/stock/out', [InventoryApiController::class, 'stockOut'])->middleware('permission:stock_out');
        Route::post('/inventory/stock/adjust', [InventoryApiController::class, 'stockAdjust'])->middleware('permission:adjust_stock');
        Route::post('/inventory/stock/transfer', [InventoryApiController::class, 'stockTransfer'])->middleware('permission:stock_out');
        Route::get('/inventory/alerts', [InventoryApiController::class, 'alerts'])->middleware('permission:inventory.read');

        // Settings
        Route::get('/settings', [SettingApiController::class, 'index'])->middleware('permission:settings.read');
        Route::get('/settings/{group}', [SettingApiController::class, 'show'])->middleware('permission:settings.read');
        Route::put('/settings/{group}', [SettingApiController::class, 'update'])->middleware('permission:settings.update');
        Route::get('/branch-settings', [SettingApiController::class, 'branchSettingsIndex'])->middleware('permission:edit_branch_settings');
        Route::put('/branch-settings', [SettingApiController::class, 'branchSettingsUpdate'])->middleware('permission:edit_branch_settings');
    });
});


