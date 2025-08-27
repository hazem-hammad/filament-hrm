<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;

    protected $table = 'contact_us';

    protected $moduleName = 'Contact us';

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    #[Scope]
    public function unread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    #[Scope]
    public function read(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }
}
