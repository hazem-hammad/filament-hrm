<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Attendance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $employeeId = auth()->id();
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // This month's stats
        $thisMonthAttendance = Attendance::where('employee_id', $employeeId)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear);
        
        $totalDaysThisMonth = $thisMonthAttendance->count();
        $lateDaysThisMonth = $thisMonthAttendance->where('late_minutes', '>', 0)->count();
        $totalMissingHours = $thisMonthAttendance->sum('missing_hours');
        $totalWorkingHours = $thisMonthAttendance->sum('working_hours');
        
        // This week's stats
        $thisWeekAttendance = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
        
        $thisWeekDays = $thisWeekAttendance->count();
        $thisWeekLate = $thisWeekAttendance->where('late_minutes', '>', 0)->count();

        return [
            Stat::make('Days This Month', $totalDaysThisMonth)
                ->description('Total attendance days')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
                
            // Stat::make('Late Days This Month', $lateDaysThisMonth)
            //     ->description($lateDaysThisMonth > 0 ? 'Try to improve punctuality' : 'Great punctuality!')
            //     ->descriptionIcon('heroicon-m-clock')
            //     ->color($lateDaysThisMonth > 0 ? 'warning' : 'success'),
                
            // Stat::make('Working Hours (Month)', number_format($totalWorkingHours, 1) . 'h')
            //     ->description('Total hours worked this month')
            //     ->descriptionIcon('heroicon-m-briefcase')
            //     ->color('info'),
                
            // Stat::make('Missing Hours (Month)', number_format($totalMissingHours, 1) . 'h')
            //     ->description($totalMissingHours > 0 ? 'Complete your working hours' : 'All hours completed!')
            //     ->descriptionIcon('heroicon-m-exclamation-triangle')
            //     ->color($totalMissingHours > 0 ? 'danger' : 'success'),
        ];
    }
    
    protected static ?string $pollingInterval = '30s';
}
