<?php

namespace App\Enum;

enum AssetCondition: string
{
    case EXCELLENT = 'excellent';
    case GOOD = 'good';
    case FAIR = 'fair';
    case POOR = 'poor';

    public function label(): string
    {
        return match($this) {
            self::EXCELLENT => 'Excellent',
            self::GOOD => 'Good',
            self::FAIR => 'Fair',
            self::POOR => 'Poor',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn($case) => [$case->value => $case->label()]
        )->toArray();
    }

    public function color(): string
    {
        return match($this) {
            self::EXCELLENT => 'success',
            self::GOOD => 'primary',
            self::FAIR => 'warning',
            self::POOR => 'danger',
        };
    }
}