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
}
