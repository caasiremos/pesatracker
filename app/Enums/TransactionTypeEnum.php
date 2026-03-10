<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case DEPOSIT = 'momo_deposit';
    case WITHDRAWAL = 'momo_withdrawal';

    case UTILITY = 'utility';

    public function label(): string
    {
        return match ($this) {
            self::DEPOSIT => 'Momo Deposit',
            self::WITHDRAWAL => 'Momo Withdrawal',
            self::UTILITY => 'Utility',
        };
    }

    public function value(): string
    {
        return match ($this) {
            self::DEPOSIT => 'momo_deposit',
            self::WITHDRAWAL => 'momo_withdrawal',
            self::UTILITY => 'member_to_member',
        };
    }
}
