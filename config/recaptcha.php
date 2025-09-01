<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Google reCAPTCHA integration.
    | You can obtain your site key and secret key from:
    | https://www.google.com/recaptcha/admin
    |
    */

    'site_key' => env('RECAPTCHA_SITE_KEY', ''),
    'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
    'enabled' => env('RECAPTCHA_ENABLED', true),
    
    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Version
    |--------------------------------------------------------------------------
    |
    | Supported versions: "v2", "v3"
    | v2: Traditional checkbox reCAPTCHA
    | v3: Invisible reCAPTCHA with score-based verification
    |
    */
    'version' => env('RECAPTCHA_VERSION', 'v3'),
    
    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA v3 Score Threshold
    |--------------------------------------------------------------------------
    |
    | For reCAPTCHA v3, this is the minimum score required to pass validation.
    | Scores range from 0.0 (likely bot) to 1.0 (likely human).
    | Recommended: 0.5
    |
    */
    'score_threshold' => env('RECAPTCHA_SCORE_THRESHOLD', 0.5),
    
    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA v3 Action
    |--------------------------------------------------------------------------
    |
    | For reCAPTCHA v3, this is the action name that will be recorded.
    | This helps distinguish between different actions on your site.
    |
    */
    'action' => env('RECAPTCHA_ACTION', 'job_application'),
    
    /*
    |--------------------------------------------------------------------------
    | Skip reCAPTCHA for Testing
    |--------------------------------------------------------------------------
    |
    | When testing is true, reCAPTCHA validation will be skipped.
    | This should only be used in testing environments.
    |
    */
    'skip_testing' => env('RECAPTCHA_SKIP_TESTING', false),
];