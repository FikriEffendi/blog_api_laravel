<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinutes(30, 3)->by($request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(1, 15)->by($request->ip());
        });

        RateLimiter::for('content', function (Request $request) {
            return Limit::perMinutes(1, 25)->by($request->ip());
        });
    }
}
