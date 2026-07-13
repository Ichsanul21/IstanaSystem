<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\Order;
use App\Observers\ActivityLogObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Order::observe(ActivityLogObserver::class);
        Expense::observe(ActivityLogObserver::class);

        Gate::define('pos-access', fn ($user) => $user->hasAnyRole(['Developer', 'Super Admin', 'Branch Admin', 'Cashier', 'Owner']));
        Gate::define('reports-access', fn ($user) => $user->hasAnyRole(['Developer', 'Super Admin', 'Branch Admin', 'Owner']));
        Gate::define('admin-access', fn ($user) => $user->hasAnyRole(['Developer', 'Super Admin', 'Branch Admin']));
        Gate::define('audit-log-access', fn ($user) => $user->hasAnyRole(['Developer', 'Super Admin', 'Owner']));
    }
}
