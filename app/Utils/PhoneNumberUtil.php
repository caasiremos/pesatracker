<?php

declare(strict_types=1);

namespace App\Utils;

class PhoneNumberUtil
{
    /**
     * Format phone number for airtel
     */
    public static function formatAirtelPhoneNumber(string $phone_number): string
    {
        if (str_starts_with($phone_number, '25675')) {
            $phone_number = substr_replace($phone_number, '', 0, 3);
        }
        if (str_starts_with($phone_number, '25674')) {
            $phone_number = substr_replace($phone_number, '', 0, 3);
        }
        if (str_starts_with($phone_number, '25670')) {
            $phone_number = substr_replace($phone_number, '', 0, 3);
        }
        if (str_starts_with($phone_number, '+25675')) {
            $phone_number = substr_replace($phone_number, '', 0, 4);
        }
        if (str_starts_with($phone_number, '+25670')) {
            $phone_number = substr_replace($phone_number, '', 0, 4);
        }
        if (str_starts_with($phone_number, '+25674')) {
            $phone_number = substr_replace($phone_number, '', 0, 4);
        }

        return $phone_number;
    }

    /**
     * Determine if provider is mtn or airtel
     */
    public static function provider(string $phone_number): string
    {
        $mtn_number_formats = ['25677', '25678', '25676','25679','256769','2567690'];
        if (in_array(substr($phone_number, 0, 5), $mtn_number_formats)) {
            return 'mtn';
        }

        return 'airtel';
    }
}
