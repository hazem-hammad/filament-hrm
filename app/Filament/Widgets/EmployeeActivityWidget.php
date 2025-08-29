<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Request;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class EmployeeActivityWidget extends Widget
{
    protected static string $view = 'filament.widgets.employee-activity';
    
    protected static ?int $sort = 4;
    
    protected static ?string $pollingInterval = '30s';
    
    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    protected function getViewData(): array
    {
        return [
            'recentEmployees' => $this->getRecentEmployees(),
            'recentRequests' => $this->getRecentRequests(),
            'upcomingEvents' => $this->getUpcomingEvents(),
            'quickStats' => $this->getQuickStats(),
        ];
    }

    private function getRecentEmployees(): \Illuminate\Support\Collection
    {
        return Employee::with(['department', 'position'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'department' => $employee->department?->name,
                    'position' => $employee->position?->name,
                    'status' => $employee->status ? 'Active' : 'Inactive',
                    'joined' => $employee->created_at->diffForHumans(),
                    'avatar' => $employee->getFirstMediaUrl('profile') ?: null,
                ];
            });
    }

    private function getRecentRequests(): \Illuminate\Support\Collection
    {
        return Request::with(['employee', 'requestable'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'employee' => $request->employee->name,
                    'type' => ucfirst($request->request_type),
                    'category' => $request->requestable?->name,
                    'status' => ucfirst($request->status),
                    'submitted' => $request->created_at->diffForHumans(),
                    'days' => $request->total_days,
                    'hours' => $request->hours,
                ];
            });
    }

    private function getUpcomingEvents(): \Illuminate\Support\Collection
    {
        // Get approved vacation requests starting in the next 7 days
        return Request::with(['employee', 'requestable'])
            ->where('status', 'approved')
            ->where('request_type', 'vacation')
            ->whereBetween('start_date', [now(), now()->addDays(7)])
            ->orderBy('start_date')
            ->limit(5)
            ->get()
            ->map(function ($request) {
                return [
                    'employee' => $request->employee->name,
                    'type' => $request->requestable?->name,
                    'start_date' => $request->start_date->format('M d, Y'),
                    'end_date' => $request->end_date->format('M d, Y'),
                    'days' => $request->total_days,
                    'starts_in' => $request->start_date->diffForHumans(),
                ];
            });
    }

    private function getQuickStats(): array
    {
        $today = now()->format('Y-m-d');
        $thisMonth = now()->format('Y-m');
        
        return [
            'employees_joined_today' => Employee::whereDate('created_at', $today)->count(),
            'requests_today' => Request::whereDate('created_at', $today)->count(),
            'pending_requests' => Request::where('status', 'pending')->count(),
            'employees_on_vacation' => Request::where('status', 'approved')
                ->where('request_type', 'vacation')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->count(),
            'requests_this_month' => Request::where('created_at', 'like', $thisMonth . '%')->count(),
            'approval_rate' => $this->getApprovalRate(),
        ];
    }

    private function getApprovalRate(): float
    {
        $thisMonth = now()->format('Y-m');
        $totalRequests = Request::where('created_at', 'like', $thisMonth . '%')->count();
        $approvedRequests = Request::where('created_at', 'like', $thisMonth . '%')
            ->where('status', 'approved')
            ->count();
            
        return $totalRequests > 0 ? round(($approvedRequests / $totalRequests) * 100, 1) : 0;
    }
}