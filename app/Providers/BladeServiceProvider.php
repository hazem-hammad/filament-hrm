<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class BladeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::if('moduleEnabled', function (string $moduleName) {
            return moduleEnabled($moduleName);
        });

        Blade::if('moduleDisabled', function (string $moduleName) {
            return !moduleEnabled($moduleName);
        });
    }
}