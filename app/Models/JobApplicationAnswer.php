<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplicationAnswer extends Model
{
    protected $fillable = [
        'job_application_id',
        'custom_question_id',
        'answer',
    ];

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function customQuestion(): BelongsTo
    {
        return $this->belongsTo(CustomQuestion::class);
    }
}
