<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VacationType extends Model
{
    protected $fillable = [
        'name',
        'balance',
        'unlock_after_months',
        'required_days_before',
        'requires_approval',
        'status',
        'description',
    ];

    protected $casts = [
        'balance' => 'integer',
        'unlock_after_months' => 'integer',
        'required_days_before' => 'integer',
        'requires_approval' => 'boolean',
        'status' => 'boolean',
    ];

    #[Scope]
    public function active(Builder $query): void
    {
        $query->where('status', true);
    }

    #[Scope]
    public function requiresApproval(Builder $query): void
    {
        $query->where('requires_approval', true);
    }

    #[Scope]
    public function immediatelyAvailable(Builder $query): void
    {
        $query->where('unlock_after_months', 0);
    }

    public function isAvailableForEmployee(\App\Models\Employee $employee): bool
    {
        if (!$this->status) {
            return false;
        }

        if ($this->unlock_after_months === 0) {
            return true;
        }

        $joiningDate = $employee->company_date_of_joining;
        $monthsSinceJoining = $joiningDate->diffInMonths(now());

        return $monthsSinceJoining >= $this->unlock_after_months;
    }

    public function getMinimumNoticeDaysAttribute(): int
    {
        return $this->required_days_before;
    }

    public function getIsImmediateAttribute(): bool
    {
        return $this->unlock_after_months === 0;
    }
}
