<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Request;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class EmployeeOverviewWidget extends ChartWidget
{
    protected static ?string $heading = 'Employee Overview';
    
    protected static ?int $sort = 2;
    
    protected static ?string $pollingInterval = '30s';
    
    public function getColumnSpan(): int | string | array
    {
        return 2;
    }

    protected function getData(): array
    {
        $employeesByDepartment = Department::withCount('employees')->get();
        
        return [
            'datasets' => [
                [
                    'label' => 'Employees by Department',
                    'data' => $employeesByDepartment->pluck('employees_count')->toArray(),
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#FF6384',
                        '#C9CBCF',
                        '#4BC0C0',
                        '#FF6384'
                    ],
                ],
            ],
            'labels' => $employeesByDepartment->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}