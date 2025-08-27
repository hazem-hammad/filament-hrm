<?php

namespace App\Filament\Employee\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Employee Dashboard';
    
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.employee.pages.dashboard';
}