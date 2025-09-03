<?php

namespace App\Enum;

enum InsuranceRelation: string
{
    case CHILD = 'child';
    case SPOUSE = 'spouse';

    public function getLabel(): string
    {
        return match ($this) {
            self::CHILD => 'Child',
            self::SPOUSE => 'Spouse',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}