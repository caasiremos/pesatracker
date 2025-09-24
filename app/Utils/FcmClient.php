<?php

namespace App\Utils;

use App\Events\WalletTransactionEvent;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class FcmClient
{
    public function __construct(
        private string $projectId,
        private string $serviceAccountJsonPath,
        private Client $http = new Client()
    ) {}

    private function accessToken(): string
    {
        $creds = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            json_decode(file_get_contents($this->serviceAccountJsonPath), true)
        );
        return $creds->fetchAuthToken()['access_token'];
    }

    public function sendToToken(string $token, array $notification, array $data = [], array $android = []): array
    {
        $payload = [
            'message' => array_filter([
                'token'        => $token,
                'notification' => $notification,          // title, body (optional if sending data-only)
                'data'         => array_map('strval', $data), // values must be strings
                'android'      => array_filter($android), // android-specific overrides
            ]),
        ];

        $resp = $this->http->post(
            "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken(),
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 10,
            ]
        );

        return json_decode((string) $resp->getBody(), true);
    }

    // app/Listeners/SendWalletDepositPush.php
    public function handle(WalletTransactionEvent $event)
    {
        $user = $event->user;
        $tokens = $user->devices()->where('active', true)->pluck('device_token');

        foreach ($tokens as $token) {
            dispatch(function () use ($token, $event) {
                app(FcmClient::class)->sendToToken(
                    token: $token,
                    notification: [
                        'title' => 'Wallet deposit',
                        'body'  => 'Your wallet was credited.',
                    ],
                    data: [
                        'type'       => 'wallet_deposit',
                        'txId'       => (string)$event->transaction_id,
                    ],
                    android: [
                        'priority'     => 'HIGH',          // delivery priority
                        'ttl'          => '3600s',         // keep for up to 1h if offline
                        'collapse_key' => 'balance_update',
                        'notification' => [
                            'channel_id'           => 'wallet_alerts',
                            'notification_priority' => 'PRIORITY_HIGH', // display priority
                            'click_action'         => 'OPEN_TX', // match an intent-filter in your AndroidManifest
                        ],
                    ]
                );
            })->onQueue('push'); // use Redis/SQS queue
        }
    }
}
