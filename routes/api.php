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
Route::middleware('auth:sanctum')->get('/customers/search', [CustomerApiController::class, 'search']);

Route::prefix('v1')->group(function () {
    // Public
    Route::get('/track/{token}', [TrackingApiController::class, 'status']);
    Route::post('/track/{token}/verify', [TrackingApiController::class, 'verify']);

    // Auth (no sanctum required for login)
    Route::post('/auth/login', [AuthApiController::class, 'login']);

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);
        Route::get('/auth/me', [AuthApiController::class, 'me']);
        Route::put('/auth/profile', [AuthApiController::class, 'updateProfile']);

        // Branches
        Route::get('/branches', [BranchApiController::class, 'index']);
        Route::get('/branches/{id}', [BranchApiController::class, 'show']);
        Route::post('/branches', [BranchApiController::class, 'store']);
        Route::put('/branches/{id}', [BranchApiController::class, 'update']);
        Route::get('/branches/{id}/daily-cash-flow', [BranchApiController::class, 'dailyCashFlow']);
        Route::get('/branches/switch/{id}', [BranchApiController::class, 'switchBranch']);
        Route::get('/workshops', [BranchApiController::class, 'workshops']);

        // Services
        Route::get('/services', [ServiceApiController::class, 'index']);
        Route::get('/services/{id}', [ServiceApiController::class, 'show']);
        Route::post('/services', [ServiceApiController::class, 'store']);
        Route::get('/service-pricings', [ServiceApiController::class, 'pricings']);
        Route::put('/service-pricings/{id}', [ServiceApiController::class, 'updatePricing']);
        Route::put('/service-pricings/bulk', [ServiceApiController::class, 'bulkUpdatePricing']);

        // Orders
        Route::apiResource('orders', OrderApiController::class);
        Route::put('/orders/{order}/status', [OrderApiController::class, 'updateStatus']);
        Route::post('/orders/{id}/payment', [OrderApiController::class, 'payment']);
        Route::post('/orders/{id}/refund', [OrderApiController::class, 'refund']);
        Route::post('/orders/{id}/receipt', [OrderApiController::class, 'receipt']);
        Route::get('/orders/{id}/tracking-status', [OrderApiController::class, 'trackingStatus']);

        // Customers
        Route::apiResource('customers', CustomerApiController::class);
        Route::get('/customers/{id}/orders', [CustomerApiController::class, 'orders']);
        Route::get('/customers/{id}/points', [CustomerApiController::class, 'points']);
        Route::post('/customers/{id}/adjust-points', [CustomerApiController::class, 'adjustPoints']);
        Route::get('/membership-tiers', [CustomerApiController::class, 'membershipTiers']);
        Route::get('/customers/lookup', [CustomerApiController::class, 'lookup']);

        // Workshop
        Route::get('/workshop/scan/{qrToken}', [\App\Http\Controllers\Api\V1\WorkshopApiController::class, 'scan']);
        Route::post('/workshop/scan/{qrToken}/update', [\App\Http\Controllers\Api\V1\WorkshopApiController::class, 'updateScanStatus']);
        Route::get('/workshop/queue', [\App\Http\Controllers\Api\V1\WorkshopApiController::class, 'queue']);
        Route::get('/workshop/stats', [\App\Http\Controllers\Api\V1\WorkshopApiController::class, 'stats']);

        // Promotions
        Route::get('/promotions', [\App\Http\Controllers\Api\V1\PromotionApiController::class, 'index']);
        Route::get('/promotions/{id}', [\App\Http\Controllers\Api\V1\PromotionApiController::class, 'show']);
        Route::post('/promotions', [\App\Http\Controllers\Api\V1\PromotionApiController::class, 'store']);
        Route::put('/promotions/{id}', [\App\Http\Controllers\Api\V1\PromotionApiController::class, 'update']);
        Route::get('/promotions/eligible/{orderId}', [\App\Http\Controllers\Api\V1\PromotionApiController::class, 'eligible']);
        Route::post('/promotions/{id}/calculate', [\App\Http\Controllers\Api\V1\PromotionApiController::class, 'calculate']);

        // Dashboard
        Route::get('/dashboard/summary', [DashboardApiController::class, 'summary']);
        Route::get('/dashboard/revenue', [DashboardApiController::class, 'revenue']);
        Route::get('/dashboard/operational', [DashboardApiController::class, 'operational']);
        Route::get('/dashboard/production', [DashboardApiController::class, 'production']);
        Route::get('/dashboard/finance', [DashboardApiController::class, 'financeData']);
        Route::get('/dashboard/inventory', [DashboardApiController::class, 'inventoryData']);

        // Payments
        Route::post('/payments/midtrans/snap', [PaymentApiController::class, 'snapToken']);
        Route::get('/payments/{orderId}/status', [PaymentApiController::class, 'status']);
        Route::post('/payments/{orderId}/verify', [PaymentApiController::class, 'verify']);

        // Finance
        Route::get('/finance/journal', [FinanceApiController::class, 'journalIndex']);
        Route::get('/finance/journal/{id}', [FinanceApiController::class, 'journalShow']);
        Route::post('/finance/journal', [FinanceApiController::class, 'journalStore']);
        Route::get('/finance/coa', [FinanceApiController::class, 'coaIndex']);
        Route::get('/finance/coa/{id}', [FinanceApiController::class, 'coaShow']);
        Route::get('/finance/coa/{id}/ledger', [FinanceApiController::class, 'coaLedger']);
        Route::get('/finance/trial-balance', [FinanceApiController::class, 'trialBalance']);
        Route::get('/finance/profit-loss', [FinanceApiController::class, 'profitLoss']);
        Route::get('/finance/balance-sheet', [FinanceApiController::class, 'balanceSheet']);
        Route::get('/finance/expenses', [FinanceApiController::class, 'expensesIndex']);
        Route::post('/finance/expenses', [FinanceApiController::class, 'expensesStore']);
        Route::get('/finance/tax/summary', [FinanceApiController::class, 'taxSummary']);
        Route::get('/finance/periods', [FinanceApiController::class, 'periodsIndex']);
        Route::post('/finance/periods', [FinanceApiController::class, 'periodsStore']);
        Route::post('/finance/periods/{id}/close', [FinanceApiController::class, 'periodsClose']);

        // Inventory
        Route::get('/inventory/items', [InventoryApiController::class, 'itemsIndex']);
        Route::get('/inventory/items/{id}', [InventoryApiController::class, 'itemsShow']);
        Route::post('/inventory/items', [InventoryApiController::class, 'itemsStore']);
        Route::put('/inventory/items/{id}', [InventoryApiController::class, 'itemsUpdate']);
        Route::get('/inventory/stock', [InventoryApiController::class, 'stockIndex']);
        Route::get('/inventory/stock/{itemId}/detail', [InventoryApiController::class, 'stockDetail']);
        Route::post('/inventory/stock/in', [InventoryApiController::class, 'stockIn']);
        Route::post('/inventory/stock/out', [InventoryApiController::class, 'stockOut']);
        Route::post('/inventory/stock/adjust', [InventoryApiController::class, 'stockAdjust']);
        Route::post('/inventory/stock/transfer', [InventoryApiController::class, 'stockTransfer']);
        Route::get('/inventory/alerts', [InventoryApiController::class, 'alerts']);

        // Settings
        Route::get('/settings', [SettingApiController::class, 'index']);
        Route::get('/settings/{group}', [SettingApiController::class, 'show']);
        Route::put('/settings/{group}', [SettingApiController::class, 'update']);
        Route::get('/branch-settings', [SettingApiController::class, 'branchSettingsIndex']);
        Route::put('/branch-settings', [SettingApiController::class, 'branchSettingsUpdate']);
    });
});

Route::post('/v1/payments/midtrans/callback', [\App\Http\Controllers\Api\V1\PaymentWebhookController::class, 'midtrans']);
