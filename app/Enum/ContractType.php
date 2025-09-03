<?php

namespace App\Enum;

enum ContractType: string
{
    case PERMANENT = 'permanent';
    case FULLTIME = 'fulltime';
    case PARTTIME = 'parttime';
    case FREELANCE = 'freelance';
    case CREDIT_HOURS = 'credit_hours';
    case INTERNSHIP = 'internship';

    public static function options(): array
    {
        return [
            self::PERMANENT->value => 'Permanent',
            self::FULLTIME->value => 'Full Time',
            self::PARTTIME->value => 'Part Time',
            self::FREELANCE->value => 'Freelance',
            self::CREDIT_HOURS->value => 'Credit Hours',
            self::INTERNSHIP->value => 'Internship',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::PERMANENT => 'Permanent',
            self::FULLTIME => 'Full Time',
            self::PARTTIME => 'Part Time',
            self::FREELANCE => 'Freelance',
            self::CREDIT_HOURS => 'Credit Hours',
            self::INTERNSHIP => 'Internship',
        };
    }
}