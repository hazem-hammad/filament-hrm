<?php

namespace App\Enum;

enum EmployeeLevel: string
{
    case INTERNSHIP = 'internship';
    case ENTRY = 'entry';
    case JUNIOR = 'junior';
    case MID = 'mid';
    case SENIOR = 'senior';
    case LEAD = 'lead';
    case MANAGER = 'manager';

    public function label(): string
    {
        return match($this) {
            self::INTERNSHIP => 'Internship',
            self::ENTRY => 'Entry',
            self::JUNIOR => 'Junior',
            self::MID => 'Mid',
            self::SENIOR => 'Senior',
            self::LEAD => 'Lead',
            self::MANAGER => 'Manager',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn($case) => [$case->value => $case->label()]
        )->toArray();
    }
}