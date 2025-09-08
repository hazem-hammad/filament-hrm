<?php

namespace App\Providers;

use App\Classes\Payment\DTOs\MyFatoorah\PaymentDTO;
use App\Classes\Payment\Payment;
use App\Classes\Payment\PaymentInterface;
use App\Enum\FilamentPanelID;
use App\Helpers\CustomLogger;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\PanelSwitch\PanelSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\EmployeeRepositoryInterface::class,
            \App\Repositories\EmployeeRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000);
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10);
        });

        RateLimiter::for('upload', function (Request $request) {
            return Limit::perMinute(10);
        });

        RateLimiter::for('post_data', function (Request $request) {
            return Limit::perMinute(5);
        });

        $this->app->singleton('custom.logger', function ($app) {
            return new CustomLogger;
        });

        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinutes(1, 5);
        });

        $this->app->bind(PaymentInterface::class, function () {
            $dto = (new PaymentDTO([]))->setMyFatoorahAsGateway();

            return Payment::create($dto);
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en'])
                ->visible(outsidePanels: true);
        });

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch->simple();
        });

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                ->excludes([FilamentPanelID::EMPLOYEE->value])
                ->labels([
                    FilamentPanelID::ADMIN->value => __('Admin'),
                    FilamentPanelID::EMPLOYEE->value => __('Employee')
                ]);
        });
    }
}
