<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum StatusEnum: int implements HasLabel
{
    case ACTIVE = 1;
    case INACTIVE = 0;

    public static function options(): array
    {
        return [
            self::ACTIVE->value => __('Active'),
            self::INACTIVE->value => __('Inactive'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => __('Active'),
            self::INACTIVE => __('Inactive'),
        };
    }
}
