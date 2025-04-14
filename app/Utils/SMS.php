<?php

namespace App\Utils;

use Illuminate\Support\Facades\Http;

class SMS
{
    private const SMS_URL = 'https://eazzyconnect.com/api/v1/sms/send';

    public static function send($phoneNumber, $message)
    {
        Logger::info('SMS SENDING ' . $phoneNumber . ' ' . $message);
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/vnd.eazzyconnect.v1',
                'Content-Type' => 'application/json',
                'apiKey' => config('services.eazzyconnect.api_key'),
            ])->post(self::SMS_URL, [
                'phone_number' => '+'.$phoneNumber,
                'message' => $message,
            ]);

            Logger::info('SMS RESPONSE ' . $response->body());
        } catch (\Throwable $th) {
            Logger::error($th);
        }
    }
}
