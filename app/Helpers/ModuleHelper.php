<?php

if (!function_exists('moduleEnabled')) {
    function moduleEnabled(string $moduleName): bool
    {
        return app(\App\Services\ModuleService::class)->isEnabled($moduleName);
    }
}

if (!function_exists('getEnabledModules')) {
    function getEnabledModules(): \Illuminate\Database\Eloquent\Collection
    {
        return app(\App\Services\ModuleService::class)->getEnabledModules();
    }
}

if (!function_exists('getMenuModules')) {
    function getMenuModules(): array
    {
        return app(\App\Services\ModuleService::class)->getMenuModules();
    }
}