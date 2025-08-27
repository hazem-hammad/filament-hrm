<?php

return [
    'table_name' => 'login_otp_codes',

    'otp_code' => [
        'length' => env('OTP_LOGIN_CODE_LENGTH', 4),
        'expires' => env('OTP_LOGIN_CODE_EXPIRES_SECONDS', 120),
    ],

    'notification_class' => \Afsakar\FilamentOtpLogin\Notifications\SendOtpCode::class,
];
