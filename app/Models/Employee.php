<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Employee extends Authenticatable implements HasMedia
{
    use HasApiTokens, InteractsWithMedia, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'date_of_birth',
        'gender',
        'email',
        'address',
        'password',
        'employee_id',
        'department_id',
        'position_id',
        'reporting_to',
        'company_date_of_joining',
        'status',
        'password_set_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'company_date_of_joining' => 'date',
        'status' => 'boolean',
        'password_set_at' => 'datetime',
        'email_verified_at' => 'datetime',
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

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reporting_to');
    }

    public function directReports(): HasMany
    {
        return $this->hasMany(Employee::class, 'reporting_to');
    }

    public function registerMediaCollections(): void
    {
        // Get all document types to create collections
        $documentTypes = \App\Models\DocumentType::query()->active()->get();

        foreach ($documentTypes as $documentType) {
            $collection = $this->addMediaCollection($documentType->name)
                ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']);

            if ($documentType->is_required) {
                $collection->onlyKeepLatest(1);
            } else {
                $collection->onlyKeepLatest(3);
            }
        }

        // Default collection for other files
        $this->addMediaCollection('other_documents')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'])
            ->onlyKeepLatest(5);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->onlyFor(['image/jpeg', 'image/jpg', 'image/png']);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->employee_id)) {
                $employee->employee_id = static::generateEmployeeId();
            }
        });
    }

    public static function generateEmployeeId(): string
    {
        $prefix = 'EMP';
        $lastEmployee = static::where('employee_id', 'like', $prefix . '%')
            ->orderBy('employee_id', 'desc')
            ->first();

        if ($lastEmployee) {
            $lastNumber = (int) substr($lastEmployee->employee_id, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
