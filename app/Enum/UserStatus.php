<?php

namespace App\Enum;

enum UserStatus: string
{
    case ACTIVE = '1';
    case SUSPENDED = '0';

    public static function values(): array
    {
        return [
            self::ACTIVE->value,
            self::SUSPENDED->value,
        ];
    }
}
