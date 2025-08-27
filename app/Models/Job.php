<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Job extends Model
{
    protected $table = 'job_openings';
    protected $fillable = [
        'title',
        'department_id',
        'position_id',
        'number_of_positions',
        'work_type',
        'work_mode',
        'experience_level',
        'status',
        'start_date',
        'end_date',
        'short_description',
        'long_description',
        'job_requirements',
        'benefits',
    ];

    protected $casts = [
        'status' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'number_of_positions' => 'integer',
    ];

    #[Scope]
    public function active(Builder $query): void
    {
        $query->where('status', true);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function customQuestions(): BelongsToMany
    {
        return $this->belongsToMany(CustomQuestion::class, 'job_custom_questions');
    }

    public function getWorkTypeOptions(): array
    {
        return [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Internship',
        ];
    }

    public function getWorkModeOptions(): array
    {
        return [
            'remote' => 'Remote',
            'onsite' => 'Onsite',
            'hybrid' => 'Hybrid',
        ];
    }

    public function getExperienceLevelOptions(): array
    {
        return [
            'entry' => 'Entry',
            'junior' => 'Junior',
            'mid' => 'Mid',
            'senior' => 'Senior',
            'lead' => 'Lead',
        ];
    }
}
