<?php

namespace App\Filament\Employee\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Employee\Widgets\CheckInOutWidget::class,
            \App\Filament\Employee\Widgets\AttendanceTableWidget::class,
            \App\Filament\Employee\Widgets\BirthdayReminderWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }
}
