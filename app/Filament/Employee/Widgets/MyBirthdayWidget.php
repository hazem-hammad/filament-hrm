<?php

namespace App\Filament\Employee\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyBirthdayWidget extends Widget
{
    protected static string $view = 'filament.employee.widgets.my-birthday-widget';

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    public function isMyBirthdayToday(): bool
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee || !$employee->date_of_birth) {
            return false;
        }

        return $employee->date_of_birth->format('m-d') === now()->format('m-d');
    }

    public function getMyAge(): int
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee || !$employee->date_of_birth) {
            return 0;
        }

        return $employee->date_of_birth->diffInYears(now());
    }

    public function getDaysLived(): int
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee || !$employee->date_of_birth) {
            return 0;
        }

        return $employee->date_of_birth->diffInDays(now());
    }

    public function getEmployee()
    {
        return Auth::guard('employee')->user();
    }

    public function shouldShowWidget(): bool
    {
        return $this->isMyBirthdayToday();
    }

    public function getViewData(): array
    {
        $employee = $this->getEmployee();
        
        return [
            'isMyBirthday' => $this->isMyBirthdayToday(),
            'myAge' => $this->getMyAge(),
            'daysLived' => $this->getDaysLived(),
            'employee' => $employee,
            'profileImage' => $employee?->getFirstMediaUrl('profile', 'thumb'),
            'shouldShow' => $this->shouldShowWidget(),
        ];
    }
}