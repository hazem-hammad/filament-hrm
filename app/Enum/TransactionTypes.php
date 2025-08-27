<?php

namespace App\Enum;

enum TransactionTypes: string
{
    case DEPOSIT = 'deposit';
    case PAYMENT = 'payment';
    case WITHDRAWAL = 'withdrawal';
    case REFUND = 'refund';
    case PENALTY = 'penalty';

    public static function values(): array
    {
        return [
            self::DEPOSIT->value => 'deposit',
            self::PAYMENT->value => 'payment',
            self::WITHDRAWAL->value => 'withdrawal',
            self::REFUND->value => 'refund',
            self::PENALTY->value => 'penalty',
        ];
    }
}
