<?php

namespace App\Models;

use App\Enum\InsuranceRelation;
use App\Enum\InsuranceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'insurance_status',
        'insurance_number',
        'insurance_relation',
        'annual_cost',
        'monthly_cost',
        'activation_date',
        'deactivation_date',
    ];

    protected $casts = [
        'insurance_status' => InsuranceStatus::class,
        'insurance_relation' => InsuranceRelation::class,
        'annual_cost' => 'decimal:2',
        'monthly_cost' => 'decimal:2',
        'activation_date' => 'date',
        'deactivation_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
