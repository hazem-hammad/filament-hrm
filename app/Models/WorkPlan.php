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
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'working_days' => 'array',
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
}
