<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum NotificationReceiversType: string implements HasLabel
{
    case ALL_USERS = 'all_users';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ALL_USERS => 'all users',
        };
    }
}
