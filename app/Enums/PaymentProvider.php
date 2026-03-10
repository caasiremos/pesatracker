<?php
namespace App\Enums;

enum PaymentProvider: string
{
    case MTN = 'Mtn';
    case AIRTEL = 'Airtel';

    public function label(): string
    {
        return match ($this) {
            self::MTN => 'MTN',
            self::AIRTEL => 'AIRTEL',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
