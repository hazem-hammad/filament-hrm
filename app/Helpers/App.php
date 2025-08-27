<?php

// get settings from the database by key

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

if (!function_exists('get_setting')) {
    function get_setting(string $key, $default = null)
    {
        return null;
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
