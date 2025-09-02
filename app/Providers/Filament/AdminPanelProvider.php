<?php

namespace App\Providers\Filament;

use Afsakar\FilamentOtpLogin\FilamentOtpLoginPlugin;
use App\Enum\FilamentPanelID;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->unsavedChangesAlerts()
            ->profile()
            ->passwordReset()
            ->id(FilamentPanelID::ADMIN->value)
            ->path(FilamentPanelID::ADMIN->value)
            ->brandLogoHeight('30px')
            ->brandName(get_setting('company_name', 'Company'))
            ->brandLogo(get_setting('logo_light', '/images/logos/logo-light.svg'))
            ->darkModeBrandLogo(get_setting('logo_dark', '/images/logos/logo-dark.svg'))
            ->login()
            ->darkMode(true)
            ->colors([
                'primary' => get_setting('primary_color', '#23B53D') ?: '#23B53D',
            ])
            ->font('Cairo', provider: GoogleFontProvider::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([
                \App\Filament\Resources\JobCategoryResource::class,
                \App\Filament\Resources\JobStageResource::class,
                \App\Filament\Resources\CustomQuestionResource::class,
                \App\Filament\Resources\JobResource::class,
                \App\Filament\Resources\JobApplicationResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\EmployeeStatsWidget::class,
                \App\Filament\Widgets\EmployeeOverviewWidget::class,
                \App\Filament\Widgets\RequestTrendsWidget::class,
                \App\Filament\Widgets\EmployeeActivityWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['en', 'ar']),
            ])
            ->authGuard('admin')
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ]);
    }
}
