<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = [
        'name',
        'status',
        'is_required',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_required' => 'boolean',
    ];

    #[Scope]
    public function active(Builder $query): void
    {
        $query->where('status', true);
    }

    #[Scope]
    public function required(Builder $query): void
    {
        $query->where('is_required', true);
    }
}
