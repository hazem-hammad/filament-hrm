<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Holiday extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'type',
        'is_recurring',
        'recurrence_type',
        'is_paid',
        'status',
        'color',
        'departments',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'is_paid' => 'boolean',
        'status' => 'boolean',
        'departments' => 'array',
    ];

    #[Scope]
    public function active(Builder $query): void
    {
        $query->where('status', true);
    }

    #[Scope]
    public function upcoming(Builder $query): void
    {
        $query->where('start_date', '>=', now()->startOfDay());
    }

    #[Scope]
    public function current(Builder $query): void
    {
        $query->where('start_date', '<=', now())
              ->where('end_date', '>=', now());
    }

    #[Scope]
    public function thisYear(Builder $query): void
    {
        $query->whereYear('start_date', now()->year);
    }

    public function getDurationAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getIsCurrentAttribute(): bool
    {
        $now = now();
        return $now->between($this->start_date, $this->end_date);
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date->isFuture();
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'public' => '#10B981',
            'religious' => '#8B5CF6',
            'national' => '#EF4444',
            'company' => '#3B82F6',
            default => '#6B7280',
        };
    }

    public function getFormattedDateRangeAttribute(): string
    {
        if ($this->start_date->isSameDay($this->end_date)) {
            return $this->start_date->format('M j, Y');
        }

        if ($this->start_date->isSameMonth($this->end_date)) {
            return $this->start_date->format('M j') . ' - ' . $this->end_date->format('j, Y');
        }

        return $this->start_date->format('M j') . ' - ' . $this->end_date->format('M j, Y');
    }

    public static function getUpcomingHolidays(int $days = 30)
    {
        return static::active()
            ->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays($days))
            ->orderBy('start_date')
            ->get();
    }

    public static function getCurrentHolidays()
    {
        return static::active()
            ->current()
            ->orderBy('start_date')
            ->get();
    }

    public function appliesToDepartment($departmentId): bool
    {
        if (is_null($this->departments)) {
            return true; // Applies to all departments
        }

        return in_array($departmentId, $this->departments);
    }
}
