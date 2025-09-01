<?php

namespace App\Providers\Filament;

use App\Enum\FilamentPanelID;
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

class EmployeePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->unsavedChangesAlerts()
            ->profile()
            ->passwordReset()
            ->id(FilamentPanelID::EMPLOYEE->value)
            ->path(FilamentPanelID::EMPLOYEE->value)
            ->topNavigation()
            ->viteTheme('resources/css/filament/employee/theme.css')
            ->brandLogoHeight('30px')
            ->favicon('/images/logos/logo-icon-light.svg')
            ->brandName(get_setting('company_name', 'Company'))
            ->brandLogo(get_setting('logo_light', '/images/logos/logo-light.svg'))
            ->darkModeBrandLogo(get_setting('logo_dark', '/images/logos/logo-dark.svg'))
            ->login()
            ->darkMode(true, true)
            ->colors([
                'primary' => '#eb4034',
            ])
            ->font('Cairo', provider: GoogleFontProvider::class)
            ->discoverResources(in: app_path('Filament/Employee/Resources'), for: 'App\\Filament\\Employee\\Resources')
            ->discoverPages(in: app_path('Filament/Employee/Pages'), for: 'App\\Filament\\Employee\\Pages')
            ->pages([
                \App\Filament\Employee\Pages\Dashboard::class,
                \App\Filament\Employee\Pages\MyProfile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Employee/Widgets'), for: 'App\\Filament\\Employee\\Widgets')
            ->widgets([
                \App\Filament\Employee\Widgets\MyBirthdayWidget::class,
                \App\Filament\Employee\Widgets\CheckInOutWidget::class,
                \App\Filament\Employee\Widgets\AttendanceTableWidget::class,
                \App\Filament\Employee\Widgets\BirthdayReminderWidget::class,
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
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['en', 'ar']),
            ])
            ->authGuard('employee')
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
