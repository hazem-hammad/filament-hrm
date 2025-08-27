<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Choice
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used with our custom keys
    |
    */

    'available_locales' => ['en'],

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    'contact_info' => [
        'whatsapp_phone' => env('CONTACT_WHATSAPP_PHONE', '20-1098415860'),
    ],

    'currency' => [
        'name' => env('CURRENCY_NAME', 'SAR'),
    ],

    'country_config' => [
        'initial' => env('COUNTRY_INITIAL', 'SA'),
        'exclude' => array_filter(explode(',', env('COUNTRY_EXCLUDE', ''))),
    ],

    'minimum_version' => '1.0.0',

    'cache_ttl' => 60 * 60 * 24,

    'otp' => [
        'fixed_mode' => env('OTP_FIXED_MODE', false),
        'expired' => 1, // with minutes
    ],

    'pagination' => [
        'default_limit' => 20,
    ],
];
