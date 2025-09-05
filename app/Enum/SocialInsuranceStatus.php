<?php

namespace App\Enum;

enum SocialInsuranceStatus: string
{
    case NOT_APPLICABLE = 'not_applicable';
    case PENDING = 'pending';
    case DONE = 'done';

    public function getLabel(): string
    {
        return match ($this) {
            self::NOT_APPLICABLE => 'N/A',
            self::PENDING => 'Pending',
            self::DONE => 'Done',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}