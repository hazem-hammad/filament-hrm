<?php

namespace App\Models;

use App\Enum\EmployeeLevel;
use App\Enum\MaritalStatus;
use App\Enum\ContractType;
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
        'business_phone',
        'date_of_birth',
        'gender',
        'marital_status',
        'email',
        'personal_email',
        'national_id',
        'address',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_phone',
        'password',
        'employee_id',
        'department_id',
        'position_id',
        'level',
        'contract_type',
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
        'level' => EmployeeLevel::class,
        'marital_status' => MaritalStatus::class,
        'contract_type' => ContractType::class,
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

    public function workPlans(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(WorkPlan::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'assigned_to');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'assigned_to');
    }

    public function medicalInsurance(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function registerMediaCollections(): void
    {
        // Profile image collection
        $this->addMediaCollection('profile')
            ->acceptsMimeTypes(['image/jpeg', 'image/jpg', 'image/png'])
            ->onlyKeepLatest(1);
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
