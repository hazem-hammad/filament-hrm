<?php

// get settings from the database by key

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

if (!function_exists('get_setting')) {
    function get_setting(string $key, $default = null)
    {
        if (!Schema::hasTable('settings')) {
            return $default;
        }

        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        // Check if this setting is a file upload
        if ($setting->type === 'file' && $setting->media_collection_name) {
            // Get the first media item from the collection (using the key as collection name)
            $media = $setting->getFirstMedia($setting->media_collection_name ?? 'default');
            return $media ? $media->getUrl() : $default;
        }

        return $setting->value;
    }
}

function getLocalesConfig(): array
{
    $locales = config('core.available_locales', ['en']);
    $localesCount = count(config('core.available_locales', ['en']));
    $columnSpan = $localesCount === 1 ? 'full' : 12 / $localesCount;

    return [
        'locales' => $locales,
        'count' => $localesCount,
        'columnSpan' => $columnSpan,
        'isSingle' => $localesCount === 1,
    ];
}
