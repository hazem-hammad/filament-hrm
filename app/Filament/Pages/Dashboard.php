<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\EmployeeStatsWidget::class,
            \App\Filament\Widgets\EmployeeOverviewWidget::class,
            \App\Filament\Widgets\RequestTrendsWidget::class,
            \App\Filament\Widgets\EmployeeActivityWidget::class,
        ];
    }
}
