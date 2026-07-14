<?php

namespace App\Providers;

use App\Models\AccountingPeriod;
use App\Models\Branch;
use App\Models\BranchSetting;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\DailyCashFlow;
use App\Models\Expense;
use App\Models\GatewayConfiguration;
use App\Models\GatewayPayment;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\LoyaltyPointsTransaction;
use App\Models\MembershipTier;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemStatusLog;
use App\Models\Payment;
use App\Models\ProductionStatus;
use App\Models\Promotion;
use App\Models\PromotionBranch;
use App\Models\PromotionUsage;
use App\Models\Refund;
use App\Models\Service;
use App\Models\ServicePricing;
use App\Models\Setting;
use App\Models\TaxConfiguration;
use App\Models\TaxLog;
use App\Models\Workshop;
use App\Observers\ActivityLogObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        AccountingPeriod::observe(ActivityLogObserver::class);
        Branch::observe(ActivityLogObserver::class);
        BranchSetting::observe(ActivityLogObserver::class);
        ChartOfAccount::observe(ActivityLogObserver::class);
        Customer::observe(ActivityLogObserver::class);
        DailyCashFlow::observe(ActivityLogObserver::class);
        Expense::observe(ActivityLogObserver::class);
        GatewayConfiguration::observe(ActivityLogObserver::class);
        GatewayPayment::observe(ActivityLogObserver::class);
        InventoryBatch::observe(ActivityLogObserver::class);
        InventoryItem::observe(ActivityLogObserver::class);
        InventoryTransaction::observe(ActivityLogObserver::class);
        JournalEntry::observe(ActivityLogObserver::class);
        JournalEntryLine::observe(ActivityLogObserver::class);
        LoyaltyPointsTransaction::observe(ActivityLogObserver::class);
        MembershipTier::observe(ActivityLogObserver::class);
        Order::observe(ActivityLogObserver::class);
        OrderItem::observe(ActivityLogObserver::class);
        OrderItemStatusLog::observe(ActivityLogObserver::class);
        Payment::observe(ActivityLogObserver::class);
        ProductionStatus::observe(ActivityLogObserver::class);
        Promotion::observe(ActivityLogObserver::class);
        PromotionBranch::observe(ActivityLogObserver::class);
        PromotionUsage::observe(ActivityLogObserver::class);
        Refund::observe(ActivityLogObserver::class);
        Service::observe(ActivityLogObserver::class);
        ServicePricing::observe(ActivityLogObserver::class);
        Setting::observe(ActivityLogObserver::class);
        TaxConfiguration::observe(ActivityLogObserver::class);
        TaxLog::observe(ActivityLogObserver::class);
        Workshop::observe(ActivityLogObserver::class);

        RateLimiter::for('api', fn(Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('tracking', fn(Request $request) => Limit::perMinute(30)->by($request->ip()));
        RateLimiter::for('webhook', fn(Request $request) => Limit::perMinute(120)->by($request->ip()));
    }
}
