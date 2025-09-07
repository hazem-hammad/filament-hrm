<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AttendanceType extends Model
{
    protected $fillable = [
        'name',
        'has_limit',
        'max_hours_per_month',
        'max_requests_per_month',
        'max_hours_per_request',
        'requires_approval',
        'status',
        'description',
    ];

    protected $casts = [
        'has_limit' => 'boolean',
        'max_hours_per_month' => 'integer',
        'max_requests_per_month' => 'integer',
        'max_hours_per_request' => 'decimal:2',
        'requires_approval' => 'boolean',
        'status' => 'boolean',
    ];

    #[Scope]
    public function active(Builder $query): void
    {
        $query->where('status', true);
    }

    #[Scope]
    public function hasLimits(Builder $query): void
    {
        $query->where('has_limit', true);
    }

    #[Scope]
    public function requiresApproval(Builder $query): void
    {
        $query->where('requires_approval', true);
    }

    #[Scope]
    public function unlimited(Builder $query): void
    {
        $query->where('has_limit', false);
    }

    public function isLimited(): bool
    {
        return $this->has_limit;
    }

    public function isUnlimited(): bool
    {
        return !$this->has_limit;
    }

    public function canRequestHours(float $requestedHours): bool
    {
        if (!$this->has_limit) {
            return true;
        }

        return $this->max_hours_per_request === null || $requestedHours <= $this->max_hours_per_request;
    }

    public function hasMonthlyLimit(): bool
    {
        return $this->has_limit && ($this->max_hours_per_month !== null || $this->max_requests_per_month !== null);
    }

    public function getMonthlyHoursLimitAttribute(): ?int
    {
        return $this->has_limit ? $this->max_hours_per_month : null;
    }

    public function getMonthlyRequestsLimitAttribute(): ?int
    {
        return $this->has_limit ? $this->max_requests_per_month : null;
    }

    public function getRequestHoursLimitAttribute(): ?float
    {
        return $this->has_limit ? $this->max_hours_per_request : null;
    }

    public function getLimitSummaryAttribute(): string
    {
        if (!$this->has_limit) {
            return 'No limits';
        }

        $limits = [];
        
        if ($this->max_hours_per_month) {
            $limits[] = "{$this->max_hours_per_month}h/month";
        }
        
        if ($this->max_requests_per_month) {
            $limits[] = "{$this->max_requests_per_month} requests/month";
        }
        
        if ($this->max_hours_per_request) {
            $limits[] = "{$this->max_hours_per_request}h/request";
        }

        return empty($limits) ? 'Limited (no specific limits set)' : implode(', ', $limits);
    }

    // Monthly validation for employee requests
    public function canEmployeeRequestThisMonth($employeeId, $requestedHours = 0, $excludeRequestId = null): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Get current month's approved usage for this employee and attendance type
        // Use request_date instead of created_at for proper monthly calculations
        $monthlyUsageQuery = \App\Models\Request::where('employee_id', $employeeId)
            ->where('request_type', 'attendance')
            ->where('requestable_type', self::class)
            ->where('requestable_id', $this->id)
            ->where('status', 'approved')
            ->whereYear('request_date', $currentYear)
            ->whereMonth('request_date', $currentMonth);

        // Exclude current request when editing
        if ($excludeRequestId) {
            $monthlyUsageQuery->where('id', '!=', $excludeRequestId);
        }

        $usedRequests = $monthlyUsageQuery->count();
        $usedHours = $monthlyUsageQuery->sum('hours') ?? 0;

        if (!$this->has_limit) {
            return [
                'can_request' => true,
                'message' => 'No limits applied for this attendance type.',
                'used_requests' => $usedRequests,
                'used_hours' => $usedHours,
                'remaining_requests' => null,
                'remaining_hours' => null
            ];
        }

        // Check request limit
        if ($this->max_requests_per_month && $usedRequests >= $this->max_requests_per_month) {
            return [
                'can_request' => false,
                'message' => "Monthly request limit reached ({$usedRequests}/{$this->max_requests_per_month})",
                'used_requests' => $usedRequests,
                'used_hours' => $usedHours,
                'remaining_requests' => 0,
                'remaining_hours' => $this->max_hours_per_month ? ($this->max_hours_per_month - $usedHours) : null
            ];
        }

        // Check hours limit
        if ($this->max_hours_per_month && ($usedHours + $requestedHours) > $this->max_hours_per_month) {
            $remaining = max(0, $this->max_hours_per_month - $usedHours);
            return [
                'can_request' => false,
                'message' => "Monthly hours limit would be exceeded. Used: {$usedHours}/{$this->max_hours_per_month} hours. Remaining: {$remaining} hours.",
                'used_requests' => $usedRequests,
                'used_hours' => $usedHours,
                'remaining_requests' => $this->max_requests_per_month ? ($this->max_requests_per_month - $usedRequests) : null,
                'remaining_hours' => $remaining
            ];
        }

        // Check per-request hours limit
        if ($this->max_hours_per_request && $requestedHours > $this->max_hours_per_request) {
            return [
                'can_request' => false,
                'message' => "Requested hours ({$requestedHours}) exceed maximum per request ({$this->max_hours_per_request})",
                'used_requests' => $usedRequests,
                'used_hours' => $usedHours,
                'remaining_requests' => $this->max_requests_per_month ? ($this->max_requests_per_month - $usedRequests) : null,
                'remaining_hours' => $this->max_hours_per_month ? ($this->max_hours_per_month - $usedHours) : null
            ];
        }

        return [
            'can_request' => true,
            'message' => 'Request can be submitted.',
            'used_requests' => $usedRequests,
            'used_hours' => $usedHours,
            'remaining_requests' => $this->max_requests_per_month ? ($this->max_requests_per_month - $usedRequests) : null,
            'remaining_hours' => $this->max_hours_per_month ? ($this->max_hours_per_month - $usedHours) : null
        ];
    }
}
