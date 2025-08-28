<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Request extends Model
{
    protected $fillable = [
        'employee_id',
        'request_type',
        'requestable_id',
        'requestable_type',
        'status',
        'reason',
        'admin_notes',
        'start_date',
        'end_date',
        'total_days',
        'request_date',
        'hours',
        'start_time',
        'end_time',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'request_date' => 'date',
        'total_days' => 'integer',
        'hours' => 'decimal:2',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
    ];

    // Enums
    public const REQUEST_TYPES = [
        'vacation' => 'Vacation',
        'attendance' => 'Attendance',
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function requestable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    #[Scope]
    public function pending(Builder $query): void
    {
        $query->where('status', 'pending');
    }

    #[Scope]
    public function approved(Builder $query): void
    {
        $query->where('status', 'approved');
    }

    #[Scope]
    public function rejected(Builder $query): void
    {
        $query->where('status', 'rejected');
    }

    #[Scope]
    public function vacation(Builder $query): void
    {
        $query->where('request_type', 'vacation');
    }

    #[Scope]
    public function attendance(Builder $query): void
    {
        $query->where('request_type', 'attendance');
    }

    // Helper Methods
    public function isVacation(): bool
    {
        return $this->request_type === 'vacation';
    }

    public function isAttendance(): bool
    {
        return $this->request_type === 'attendance';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function canBeApproved(): bool
    {
        return $this->isPending();
    }

    public function canBeRejected(): bool
    {
        return $this->isPending();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    // Business Logic Methods
    public function calculateTotalDays(): int
    {
        if (!$this->isVacation() || !$this->start_date || !$this->end_date) {
            return 0;
        }

        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getEmployeeRemainingBalance(): int
    {
        if (!$this->isVacation() || !$this->requestable) {
            return 0;
        }

        // Get total approved vacation days for this year for this vacation type
        $currentYear = now()->year;
        $usedDays = self::where('employee_id', $this->employee_id)
            ->where('requestable_type', VacationType::class)
            ->where('requestable_id', $this->requestable_id)
            ->where('status', 'approved')
            ->whereYear('start_date', $currentYear)
            ->sum('total_days');

        return max(0, $this->requestable->balance - $usedDays);
    }

    public function getEmployeeMonthlyAttendanceUsage(): array
    {
        if (!$this->isAttendance() || !$this->requestable) {
            return ['hours' => 0, 'requests' => 0];
        }

        $currentMonth = now()->format('Y-m');
        $monthlyUsage = self::where('employee_id', $this->employee_id)
            ->where('requestable_type', AttendanceType::class)
            ->where('requestable_id', $this->requestable_id)
            ->where('status', 'approved')
            ->where('request_date', 'like', $currentMonth . '%')
            ->selectRaw('SUM(hours) as total_hours, COUNT(*) as total_requests')
            ->first();

        return [
            'hours' => $monthlyUsage->total_hours ?? 0,
            'requests' => $monthlyUsage->total_requests ?? 0,
        ];
    }

    public function isVacationTypeAvailable(): bool
    {
        if (!$this->isVacation() || !$this->requestable) {
            return false;
        }

        return $this->requestable->isAvailableForEmployee($this->employee);
    }

    public function validateRequest(): array
    {
        $errors = [];

        if ($this->isVacation()) {
            $errors = array_merge($errors, $this->validateVacationRequest());
        } elseif ($this->isAttendance()) {
            $errors = array_merge($errors, $this->validateAttendanceRequest());
        }

        return $errors;
    }

    protected function validateVacationRequest(): array
    {
        $errors = [];

        if (!$this->requestable) {
            $errors[] = 'Vacation type is required.';
            return $errors;
        }

        // Check if vacation type is available for employee
        if (!$this->isVacationTypeAvailable()) {
            $errors[] = "This vacation type is not yet available. You need to wait {$this->requestable->unlock_after_months} months after joining.";
        }

        // Check remaining balance
        $remainingBalance = $this->getEmployeeRemainingBalance();
        if ($this->total_days > $remainingBalance) {
            $errors[] = "Insufficient balance. You have {$remainingBalance} days remaining for this vacation type.";
        }

        // Check notice period
        if ($this->requestable->required_days_before > 0) {
            $noticeDate = now()->addDays($this->requestable->required_days_before);
            if ($this->start_date && $this->start_date->lt($noticeDate)) {
                $errors[] = "This vacation type requires {$this->requestable->required_days_before} days advance notice.";
            }
        }

        return $errors;
    }

    protected function validateAttendanceRequest(): array
    {
        $errors = [];

        if (!$this->requestable) {
            $errors[] = 'Attendance type is required.';
            return $errors;
        }

        // Check if attendance type has limits
        if ($this->requestable->has_limit) {
            $monthlyUsage = $this->getEmployeeMonthlyAttendanceUsage();

            // Check monthly hour limit
            if ($this->requestable->max_hours_per_month) {
                $totalHoursAfterRequest = $monthlyUsage['hours'] + $this->hours;
                if ($totalHoursAfterRequest > $this->requestable->max_hours_per_month) {
                    $remaining = $this->requestable->max_hours_per_month - $monthlyUsage['hours'];
                    $errors[] = "Monthly hour limit exceeded. You have {$remaining} hours remaining this month.";
                }
            }

            // Check monthly request limit
            if ($this->requestable->max_requests_per_month) {
                $totalRequestsAfterRequest = $monthlyUsage['requests'] + 1;
                if ($totalRequestsAfterRequest > $this->requestable->max_requests_per_month) {
                    $remaining = $this->requestable->max_requests_per_month - $monthlyUsage['requests'];
                    $errors[] = "Monthly request limit exceeded. You have {$remaining} requests remaining this month.";
                }
            }

            // Check per-request hour limit
            if ($this->requestable->max_hours_per_request && $this->hours > $this->requestable->max_hours_per_request) {
                $errors[] = "Request exceeds maximum hours per request ({$this->requestable->max_hours_per_request} hours).";
            }
        }

        return $errors;
    }

    // Accessor for status badge color
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    // Accessor for request type badge color
    public function getRequestTypeColorAttribute(): string
    {
        return match ($this->request_type) {
            'vacation' => 'info',
            'attendance' => 'primary',
            default => 'gray',
        };
    }
}
