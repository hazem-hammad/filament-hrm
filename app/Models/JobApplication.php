<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class JobApplication extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'job_id',
        'job_stage_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'linkedin_url',
        'years_of_experience',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'years_of_experience' => 'integer',
    ];

    #[Scope]
    public function active(Builder $query): void
    {
        $query->where('status', true);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function jobStage(): BelongsTo
    {
        return $this->belongsTo(JobStage::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(JobApplicationAnswer::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function registerMediaCollections(): void
    {
        $allowedMimeTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Excel .xlsx
            'application/vnd.ms-excel', // Excel .xls
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'text/plain',
            'text/csv',
        ];

        $this->addMediaCollection('resume')
            ->singleFile()
            ->acceptsMimeTypes($allowedMimeTypes);
        
        $this->addMediaCollection('custom_questions')
            ->acceptsMimeTypes($allowedMimeTypes);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($jobApplication) {
            if (empty($jobApplication->job_stage_id)) {
                // Assign to the first job stage based on sort order
                $firstStage = JobStage::query()
                    ->active()
                    ->orderBy('sort')
                    ->first();
                
                if ($firstStage) {
                    $jobApplication->job_stage_id = $firstStage->id;
                }
            }
        });
    }
}
