<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NotificationCenter extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'body'];

    protected $moduleName = 'Notifications';

    protected $fillable = ['title', 'body', 'send_to'];

    protected $casts = [
        'name' => 'array',
    ];

    public function getModuleName(): string
    {
        return $this->moduleName;
    }
}
