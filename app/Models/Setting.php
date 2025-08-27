<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'group',
        'name',
        'key',
        'value',
        'type',
        'is_configurable_by_admin',
        'media_collection_name'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo_light')
            ->singleFile();

        $this->addMediaCollection('logo_dark')
            ->singleFile();
    }
}
