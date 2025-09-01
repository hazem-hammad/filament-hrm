<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Job extends Model
{
    use HasFactory;
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
        'slug',
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

    protected static function booted(): void
    {
        static::creating(function (Job $model) {
            if (empty($model->slug) && !empty($model->title)) {
                $base = Str::slug($model->title);
                $slug = $base;
                $i = 1;
                while (self::query()->where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $model->slug = $slug;
            }
        });

        static::updating(function (Job $model) {
            // If title changed and slug wasn't explicitly modified, regenerate slug
            if ($model->isDirty('title') && !$model->isDirty('slug')) {
                $base = Str::slug($model->title);
                $slug = $base;
                $i = 1;
                while (self::query()
                    ->where('slug', $slug)
                    ->where('id', '<>', $model->getKey())
                    ->exists()
                ) {
                    $slug = $base . '-' . $i++;
                }
                $model->slug = $slug;
            }
        });
    }
}
