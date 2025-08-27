<?php

namespace App\Enum;

enum UserType: string
{
    case LEARNER = 'learner';
    case EXPERT = 'expert';

    public static function values(): array
    {
        return [
            self::LEARNER->value => 'learner',
            self::EXPERT->value => 'expert',
        ];
    }

    public static function getEntities(): array
    {
        return [
            self::LEARNER->value,
            self::EXPERT->value,
        ];
    }
}
