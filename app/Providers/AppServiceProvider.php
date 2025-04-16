<?php

namespace App\Providers;

use App\Models\PaymentMethod;
use App\Observers\PaymentMethodObserver;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PaymentMethod::observe(PaymentMethodObserver::class);
    }
}
