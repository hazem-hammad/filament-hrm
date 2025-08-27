<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomQuestion extends Model
{
    protected $fillable = [
        'title',
        'status',
        'is_required',
        'type',
        'options',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_required' => 'boolean',
        'options' => 'array',
    ];

    #[Scope]
    public function active(Builder $query): void
    {
        $query->where('status', true);
    }

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_custom_questions');
    }

    public function getTypeOptions(): array
    {
        return [
            'text_field' => 'Text Field',
            'date' => 'Date',
            'textarea' => 'Textarea',
            'file_upload' => 'File Upload',
            'toggle' => 'Toggle',
            'multi_select' => 'Multi Select',
        ];
    }
}
