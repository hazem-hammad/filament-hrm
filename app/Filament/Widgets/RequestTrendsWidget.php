<?php

namespace App\Filament\Widgets;

use App\Models\Request;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RequestTrendsWidget extends ChartWidget
{
    protected static ?string $heading = 'Request Trends (Last 6 Months)';
    
    protected static ?int $sort = 3;
    
    protected static ?string $pollingInterval = '30s';
    
    public function getColumnSpan(): int | string | array
    {
        return 2;
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(function ($monthsBack) {
            $date = now()->subMonths($monthsBack);
            return [
                'month' => $date->format('M Y'),
                'vacation' => Request::query()->vacation()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'attendance' => Request::query()->attendance()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'approved' => Request::query()->approved()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'rejected' => Request::query()->rejected()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Vacation Requests',
                    'data' => $months->pluck('vacation')->toArray(),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Attendance Requests',
                    'data' => $months->pluck('attendance')->toArray(),
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Approved',
                    'data' => $months->pluck('approved')->toArray(),
                    'borderColor' => '#059669',
                    'backgroundColor' => 'rgba(5, 150, 105, 0.1)',
                    'fill' => false,
                ],
                [
                    'label' => 'Rejected',
                    'data' => $months->pluck('rejected')->toArray(),
                    'borderColor' => '#DC2626',
                    'backgroundColor' => 'rgba(220, 38, 38, 0.1)',
                    'fill' => false,
                ],
            ],
            'labels' => $months->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}