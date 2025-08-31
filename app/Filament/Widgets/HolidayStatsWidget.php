<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class HolidayStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now = now();
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();

        // Basic counts
        $totalHolidays = Holiday::active()->count();
        $holidaysThisYear = Holiday::active()->thisYear()->count();
        $currentHolidays = Holiday::getCurrentHolidays()->count();
        $upcomingHolidays = Holiday::getUpcomingHolidays(30)->count();

        // Holiday types breakdown
        $publicHolidays = Holiday::active()->thisYear()->where('type', 'public')->count();
        $religiousHolidays = Holiday::active()->thisYear()->where('type', 'religious')->count();
        $nationalHolidays = Holiday::active()->thisYear()->where('type', 'national')->count();
        $companyHolidays = Holiday::active()->thisYear()->where('type', 'company')->count();

        // Paid vs unpaid
        $paidHolidays = Holiday::active()->thisYear()->where('is_paid', true)->count();
        $unpaidHolidays = Holiday::active()->thisYear()->where('is_paid', false)->count();

        // Days off calculation
        $totalDaysOff = Holiday::active()
            ->thisYear()
            ->get()
            ->sum(function ($holiday) {
                return $holiday->start_date->diffInDays($holiday->end_date) + 1;
            });

        return [
            Stat::make('Total Holidays This Year', $holidaysThisYear)
                ->description('Active holidays in ' . $now->year)
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->chart([3, 5, 10, 15, 20, 18, $holidaysThisYear]),

            Stat::make('Currently Active', $currentHolidays)
                ->description('Holidays happening now')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color($currentHolidays > 0 ? 'success' : 'gray')
                ->chart([0, 1, 0, 2, 1, 0, $currentHolidays]),

            Stat::make('Upcoming (30 days)', $upcomingHolidays)
                ->description('Holidays in the next month')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info')
                ->chart([2, 3, 1, 4, 2, 1, $upcomingHolidays]),

            Stat::make('Total Days Off', $totalDaysOff)
                ->description('Holiday days this year')
                ->descriptionIcon('heroicon-m-calendar-x')
                ->color('warning'),

            Stat::make('Public Holidays', $publicHolidays)
                ->description('Government holidays')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),

            Stat::make('Company Holidays', $companyHolidays)
                ->description('Internal company holidays')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('info'),

            Stat::make('Paid Holidays', $paidHolidays)
                ->description($unpaidHolidays > 0 ? $unpaidHolidays . ' unpaid' : 'All holidays are paid')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Holiday Types', '4')
                ->description("Public: $publicHolidays, Religious: $religiousHolidays, National: $nationalHolidays, Company: $companyHolidays")
                ->descriptionIcon('heroicon-m-tag')
                ->color('gray'),
        ];
    }
}