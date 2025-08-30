<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Employee;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class BirthdayReminderWidget extends Widget
{
    protected static string $view = 'filament.employee.widgets.birthday-reminder-widget';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    public function getUpcomingBirthdays(): Collection
    {
        $today = Carbon::today();
        $thirtyDaysLater = $today->copy()->addDays(30);
        
        // Get current year for birthday calculation
        $currentYear = $today->year;
        
        return Employee::query()
            ->active()
            ->with(['department', 'position'])
            ->whereNotNull('date_of_birth')
            ->get()
            ->filter(function ($employee) use ($today, $thirtyDaysLater, $currentYear) {
                $birthday = $employee->date_of_birth->copy()->year($currentYear);
                
                // If birthday has passed this year, check next year's birthday
                if ($birthday->lt($today)) {
                    $birthday = $birthday->copy()->year($currentYear + 1);
                }
                
                return $birthday->between($today, $thirtyDaysLater);
            })
            ->map(function ($employee) use ($currentYear, $today) {
                $birthday = $employee->date_of_birth->copy()->year($currentYear);
                
                // If birthday has passed this year, use next year
                if ($birthday->lt($today)) {
                    $birthday = $birthday->copy()->year($currentYear + 1);
                }
                
                $employee->upcoming_birthday = $birthday;
                $employee->days_until_birthday = $today->diffInDays($birthday);
                $employee->is_today = $birthday->isToday();
                $employee->is_this_week = $birthday->between($today, $today->copy()->addDays(7));
                
                // Calculate age they will turn
                $employee->turning_age = $birthday->year - $employee->date_of_birth->year;
                
                return $employee;
            })
            ->sortBy('days_until_birthday')
            ->values();
    }

    public function getTodaysBirthdays(): Collection
    {
        return $this->getUpcomingBirthdays()->where('is_today', true);
    }

    public function getThisWeekBirthdays(): Collection
    {
        return $this->getUpcomingBirthdays()->where('is_this_week', true)->where('is_today', false);
    }

    public function getUpcomingBirthdaysExcludingWeek(): Collection
    {
        return $this->getUpcomingBirthdays()->where('is_this_week', false);
    }

    public function getViewData(): array
    {
        $allBirthdays = $this->getUpcomingBirthdays();
        
        return [
            'todaysBirthdays' => $this->getTodaysBirthdays(),
            'thisWeekBirthdays' => $this->getThisWeekBirthdays(),
            'upcomingBirthdays' => $this->getUpcomingBirthdaysExcludingWeek(),
            'totalUpcoming' => $allBirthdays->count(),
            'hasAnyBirthdays' => $allBirthdays->isNotEmpty(),
        ];
    }
}