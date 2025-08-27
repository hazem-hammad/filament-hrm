<?php

namespace App\Enum;

enum OtpActions: string
{
    case VERIFY_EMAIL = 'verify-email';
    case CHANGE_EMAIL = 'change-email';
    case RESET_PASSWORD = 'reset-password';
    case COMPLETE_PROFILE = 'complete-profile';
    case VERIFY_PHONE = 'verify-phone';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
