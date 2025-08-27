<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserVerification;
use App\Observers\UserObserver;
use App\Observers\UserVerificationObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        UserVerification::observe(UserVerificationObserver::class);
        User::observe(UserObserver::class);
    }
}
