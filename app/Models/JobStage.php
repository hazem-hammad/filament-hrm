<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JobStage extends Model
{
    protected $fillable = [
        'name',
        'status',
        'sort',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort' => 'integer',
    ];

    #[Scope]
    public function active(Builder $query): void
    {
        $query->where('status', true);
    }

    #[Scope]
    public function sorted(Builder $query): void
    {
        $query->orderBy('sort');
    }
}
