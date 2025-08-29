<?php

namespace App\Models;

use App\Enum\WorkingDay;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkPlan extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'working_days',
        'permission_minutes',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'working_days' => 'array',
        'permission_minutes' => 'integer',
        'status' => 'boolean',
    ];

    #[Scope]
    public function active(Builder $query): void
    {
        $query->where('status', true);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class);
    }

    public function getWorkingDaysLabelsAttribute(): string
    {
        if (!$this->working_days) {
            return 'No days selected';
        }

        return collect($this->working_days)
            ->map(fn($day) => WorkingDay::from($day)->label())
            ->join(', ');
    }

    public function getScheduleAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    public function getPermissionMinutesLabelAttribute(): string
    {
        if ($this->permission_minutes === 0) {
            return 'No grace period';
        }

        $hours = intdiv($this->permission_minutes, 60);
        $minutes = $this->permission_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m grace period";
        } elseif ($hours > 0) {
            return "{$hours}h grace period";
        } else {
            return "{$minutes}m grace period";
        }
    }
}
