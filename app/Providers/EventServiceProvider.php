<?php

namespace App\Providers;

use App\Models\JobApplication;
use App\Observers\JobApplicationObserver;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
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
        JobApplication::observe(JobApplicationObserver::class);
    }
}
