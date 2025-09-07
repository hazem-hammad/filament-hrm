<?php

namespace App\Filament\Employee\Pages;

use App\Models\VacationType;
use App\Models\AttendanceType;
use App\Models\Request;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyBalance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.employee.pages.my-balance';

    protected static ?string $title = 'My Balance';

    protected static ?string $navigationLabel = 'My Balance';

    protected static ?int $navigationSort = 5;

    public function getViewData(): array
    {
        $employee = Auth::guard('employee')->user();
        $currentYear = Carbon::now()->year;

        // Get vacation types and calculate remaining balance
        $vacationBalances = VacationType::where('status', true)->get()->map(function ($vacationType) use ($employee, $currentYear) {
            $usedDays = Request::where('employee_id', $employee->id)
                ->where('request_type', 'vacation')
                ->where('requestable_type', VacationType::class)
                ->where('requestable_id', $vacationType->id)
                ->where('status', 'approved')
                ->whereYear('created_at', $currentYear)
                ->sum('total_days');

            $remaining = max(0, $vacationType->balance - $usedDays);
            $percentage = $vacationType->balance > 0 ? round(($remaining / $vacationType->balance) * 100) : 0;

            return [
                'type' => $vacationType,
                'used' => $usedDays,
                'remaining' => $remaining,
                'percentage' => $percentage,
                'total' => $vacationType->balance
            ];
        });

        // Get attendance types and calculate usage
        $attendanceBalances = AttendanceType::where('status', true)->get()->map(function ($attendanceType) use ($employee, $currentYear) {
            // Get current month's usage for this attendance type
            $currentMonth = Carbon::now()->month;
            $currentMonthUsage = Request::where('employee_id', $employee->id)
                ->where('request_type', 'attendance')
                ->where('requestable_type', AttendanceType::class)
                ->where('requestable_id', $attendanceType->id)
                ->where('status', 'approved')
                ->whereYear('request_date', $currentYear)
                ->whereMonth('request_date', $currentMonth);

            $usedRequests = $currentMonthUsage->count();
            $usedHours = $currentMonthUsage->sum('hours') ?? 0;

            // Calculate remaining based on limits
            if ($attendanceType->has_limit) {
                $maxRequests = $attendanceType->max_requests_per_month ?? 0;
                $maxHours = $attendanceType->max_hours_per_month ?? 0;
                
                $remainingRequests = max(0, $maxRequests - $usedRequests);
                $remainingHours = max(0, $maxHours - $usedHours);
                
                // Use requests as primary metric, hours as secondary
                if ($maxRequests > 0) {
                    $percentage = round(($remainingRequests / $maxRequests) * 100);
                    $total = $maxRequests;
                    $remaining = $remainingRequests;
                    $used = $usedRequests;
                    $unit = 'requests';
                } elseif ($maxHours > 0) {
                    $percentage = round(($remainingHours / $maxHours) * 100);
                    $total = $maxHours;
                    $remaining = $remainingHours;
                    $used = $usedHours;
                    $unit = 'hours';
                } else {
                    $percentage = 100;
                    $total = 0;
                    $remaining = 0;
                    $used = $usedRequests;
                    $unit = 'requests';
                }
            } else {
                // No limit - show unlimited
                $percentage = 100;
                $total = 'Unlimited';
                $remaining = 'Unlimited';
                $used = $usedRequests;
                $unit = 'requests';
            }

            return [
                'type' => $attendanceType,
                'used' => $used,
                'usedRequests' => $usedRequests,
                'usedHours' => $usedHours,
                'remaining' => $remaining,
                'percentage' => $percentage,
                'total' => $total,
                'unit' => $unit,
                'hasLimit' => $attendanceType->has_limit,
                'maxRequestsPerMonth' => $attendanceType->max_requests_per_month,
                'maxHoursPerMonth' => $attendanceType->max_hours_per_month,
            ];
        });

        return [
            'vacationBalances' => $vacationBalances,
            'attendanceBalances' => $attendanceBalances,
            'currentYear' => $currentYear,
            'currentMonth' => Carbon::now()->format('F Y'),
            'employee' => $employee
        ];
    }
}
