<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'work_plan_id',
        'date',
        'check_in_time',
        'check_out_time',
        'working_hours',
        'missing_hours',
        'late_minutes',
        'notes',
        'is_manual',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'working_hours' => 'decimal:2',
        'missing_hours' => 'decimal:2',
        'late_minutes' => 'integer',
        'is_manual' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function workPlan(): BelongsTo
    {
        return $this->belongsTo(WorkPlan::class);
    }

    public function getDayNameAttribute(): string
    {
        return $this->date->format('l');
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function getWorkingHoursLabelAttribute(): string
    {
        return number_format($this->working_hours, 2) . 'h';
    }

    public function getMissingHoursLabelAttribute(): string
    {
        return number_format($this->missing_hours, 2) . 'h';
    }

    public function getLateMinutesLabelAttribute(): string
    {
        if ($this->late_minutes === 0) {
            return 'On time';
        }
        
        $hours = intdiv($this->late_minutes, 60);
        $minutes = $this->late_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m late";
        } elseif ($hours > 0) {
            return "{$hours}h late";
        } else {
            return "{$minutes}m late";
        }
    }

    public function calculateWorkingHours(): float
    {
        if (!$this->check_out_time) {
            return 0;
        }

        $checkIn = Carbon::parse($this->check_in_time);
        $checkOut = Carbon::parse($this->check_out_time);
        
        return round($checkOut->diffInMinutes($checkIn) / 60, 2);
    }

    public function calculateMissingHours(): float
    {
        $workPlan = $this->workPlan;
        if (!$workPlan) {
            return 0;
        }

        $expectedStart = Carbon::parse($workPlan->start_time);
        $expectedEnd = Carbon::parse($workPlan->end_time);
        $expectedHours = round($expectedEnd->diffInMinutes($expectedStart) / 60, 2);
        
        $actualHours = $this->working_hours;
        
        return max(0, $expectedHours - $actualHours);
    }

    public function calculateLateMinutes(): int
    {
        $workPlan = $this->workPlan;
        if (!$workPlan) {
            return 0;
        }

        $expectedStart = Carbon::parse($workPlan->start_time);
        $actualStart = Carbon::parse($this->check_in_time);
        
        // Only calculate late minutes if actual check-in is after expected start time
        if ($actualStart->lte($expectedStart)) {
            return 0; // Employee is on time or early
        }

        $lateMinutes = $expectedStart->diffInMinutes($actualStart);
        
        // Apply grace period
        $lateMinutes = max(0, $lateMinutes - $workPlan->permission_minutes);
        
        return $lateMinutes;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($attendance) {
            $attendance->working_hours = $attendance->calculateWorkingHours();
            $attendance->missing_hours = $attendance->calculateMissingHours();
            $attendance->late_minutes = $attendance->calculateLateMinutes();
        });
    }
}
