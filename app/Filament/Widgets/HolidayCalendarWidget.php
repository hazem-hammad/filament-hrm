<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use Filament\Widgets\Widget;
use Carbon\Carbon;

class HolidayCalendarWidget extends Widget
{
    protected static string $view = 'filament.widgets.holiday-calendar-widget';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    public function getCurrentMonthHolidays()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return Holiday::active()
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('start_date', '<=', $startOfMonth)
                          ->where('end_date', '>=', $endOfMonth);
                    });
            })
            ->orderBy('start_date')
            ->get();
    }

    public function getUpcomingHolidays()
    {
        return Holiday::getUpcomingHolidays(90);
    }

    public function getCurrentHolidays()
    {
        return Holiday::getCurrentHolidays();
    }

    public function getCalendarData()
    {
        $today = now();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();
        
        // Get start of calendar (Sunday of the week containing the first day of month)
        $calendarStart = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        
        // Get end of calendar (Saturday of the week containing the last day of month)  
        $calendarEnd = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);
        
        $holidays = $this->getCurrentMonthHolidays();
        
        $calendarDays = [];
        $current = $calendarStart->copy();
        
        while ($current <= $calendarEnd) {
            $dayHolidays = $holidays->filter(function ($holiday) use ($current) {
                return $current->between($holiday->start_date, $holiday->end_date);
            });
            
            $calendarDays[] = [
                'date' => $current->copy(),
                'day' => $current->day,
                'isCurrentMonth' => $current->month === $today->month,
                'isToday' => $current->isToday(),
                'holidays' => $dayHolidays,
            ];
            
            $current->addDay();
        }
        
        return $calendarDays;
    }

    public function getViewData(): array
    {
        return [
            'currentMonth' => now()->format('F Y'),
            'calendarDays' => $this->getCalendarData(),
            'upcomingHolidays' => $this->getUpcomingHolidays(),
            'currentHolidays' => $this->getCurrentHolidays(),
            'totalHolidaysThisYear' => Holiday::active()->thisYear()->count(),
        ];
    }
}