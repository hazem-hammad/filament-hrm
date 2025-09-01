<?php

namespace App\Enum;

enum AssetStatus: string
{
    case AVAILABLE = 'available';
    case ASSIGNED = 'assigned';
    case MAINTENANCE = 'maintenance';
    case RETIRED = 'retired';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Available',
            self::ASSIGNED => 'Assigned',
            self::MAINTENANCE => 'Maintenance',
            self::RETIRED => 'Retired',
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
            self::AVAILABLE => 'success',
            self::ASSIGNED => 'primary',
            self::MAINTENANCE => 'warning',
            self::RETIRED => 'danger',
        };
    }
}