<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\Request;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class EmployeeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected static ?string $pollingInterval = '30s';
    
    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    protected function getStats(): array
    {
        return [
            // Total Employees
            Stat::make('Total Employees', Employee::count())
                ->description('All registered employees')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart($this->getEmployeeGrowthChart()),

            // Active Employees
            Stat::make('Active Employees', Employee::query()->active()->count())
                ->description('Currently active employees')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart($this->getActiveEmployeeChart()),

            // Total Departments
            Stat::make('Departments', Department::count())
                ->description('Active departments')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),

            // Total Positions
            Stat::make('Positions', Position::count())
                ->description('Available positions')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning'),

            // New Employees This Month
            Stat::make('New This Month', $this->getNewEmployeesThisMonth())
                ->description('New employees joined')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success')
                ->chart($this->getNewEmployeesChart()),

            // Pending Requests
            Stat::make('Pending Requests', Request::query()->pending()->count())
                ->description('Awaiting approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger')
                ->url('/admin/requests?tableFilters[status][0]=pending'),

            // Employee Distribution by Status
            Stat::make('Active Rate', $this->getActiveEmployeePercentage() . '%')
                ->description('Employee activation rate')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($this->getActiveEmployeePercentage() > 90 ? 'success' : ($this->getActiveEmployeePercentage() > 70 ? 'warning' : 'danger')),

            // Average Employees per Department
            Stat::make('Avg per Department', $this->getAverageEmployeesPerDepartment())
                ->description('Employee distribution')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('info'),
        ];
    }

    private function getEmployeeGrowthChart(): array
    {
        $months = collect(range(5, 0))->map(function ($monthsBack) {
            return Employee::whereDate('created_at', '<=', now()->subMonths($monthsBack)->endOfMonth())
                ->count();
        });

        return $months->toArray();
    }

    private function getActiveEmployeeChart(): array
    {
        $months = collect(range(5, 0))->map(function ($monthsBack) {
            return Employee::query()->active()
                ->whereDate('created_at', '<=', now()->subMonths($monthsBack)->endOfMonth())
                ->count();
        });

        return $months->toArray();
    }

    private function getNewEmployeesThisMonth(): int
    {
        return Employee::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    private function getNewEmployeesChart(): array
    {
        $days = collect(range(6, 0))->map(function ($daysBack) {
            return Employee::whereDate('created_at', now()->subDays($daysBack)->format('Y-m-d'))
                ->count();
        });

        return $days->toArray();
    }

    private function getActiveEmployeePercentage(): float
    {
        $total = Employee::count();
        $active = Employee::query()->active()->count();
        
        return $total > 0 ? round(($active / $total) * 100, 1) : 0;
    }

    private function getAverageEmployeesPerDepartment(): string
    {
        $departments = Department::count();
        $employees = Employee::count();
        
        return $departments > 0 ? number_format($employees / $departments, 1) : '0';
    }
}