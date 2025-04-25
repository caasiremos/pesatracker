<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'eazzyconnect' => [
        'api_key' => env('EZZY_CONNECT_API_KEY'),
    ],

    'telecom' => [
        'airtel_pin' => env('AIRTEL_PIN'),
        'airtel_client_id' => env('AIRTEL_CLIENT_ID'),
        'airtel_client_secrete' => env('AIRTEL_CLIENT_SECRETE'),
        'airtel_grant_type' => env('AIRTEL_CLIENT_GRANT_TYPE'),

        'mtn_api_user' => env('MTN_API_USER'),
        'mtn_api_key' => env('MTN_API_KEY'),
        'mtn_disbursement_key' => env('MTN_DISBURSEMENT_SUBSCRIPTION_KEY'),
        'mtn_collection_key' => env('MTN_COLLECTION_SUBSCRIPTION_KEY'),
    ],
];
