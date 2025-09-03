<?php

namespace App\Enum;

enum MaritalStatus: string
{
    case SINGLE = 'single';
    case MARRIED = 'married';

    public static function options(): array
    {
        return [
            self::SINGLE->value => 'Single',
            self::MARRIED->value => 'Married',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::SINGLE => 'Single',
            self::MARRIED => 'Married',
        };
    }
}