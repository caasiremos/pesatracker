<?php
namespace App\Http\Services\Firebase;

use App\Exceptions\ExpectedException;
use App\Utils\Logger;
use Google\Client as GoogleClient;
use GuzzleHttp\Client as HttpClient;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmClient
{
    private $googleClient;
    private $httpClient;

    public function __construct()
    {
        $credentialsPath = $this->resolveCredentialsPath();
        $this->googleClient = new GoogleClient();
        $this->googleClient->setAuthConfig($credentialsPath);
        $this->googleClient->addScope('https://fcm.googleapis.com/auth/firebase.messaging');
        $this->httpClient = new HttpClient();
    }

    /**
     * Resolve Firebase credentials path from config (respects FIREBASE_CREDENTIALS and GOOGLE_APPLICATION_CREDENTIALS).
     * Checks both project app/Firebase/ and storage/app/Firebase/ (capital F for Linux case-sensitivity).
     */
    private function resolveCredentialsPath(): string
    {
        $defaultProject = config('firebase.default', 'app');
        $credentials = config("firebase.projects.{$defaultProject}.credentials", 'app/Firebase/firebase_credentials.json');
        if (is_array($credentials)) {
            $credentials = $credentials['file'] ?? $credentials['path'] ?? 'app/Firebase/firebase_credentials.json';
        }
        $path = is_string($credentials) ? $credentials : 'app/Firebase/firebase_credentials.json';
        // Absolute path (Unix or Windows) from env
        if (str_starts_with($path, '/') || (strlen($path) >= 2 && $path[1] === ':')) {
            if (is_file($path)) {
                return $path;
            }
            throw new \InvalidArgumentException(
                'Firebase credentials file not found: '.$path.'. Set FIREBASE_CREDENTIALS in .env to the correct path (use capital F in Firebase: storage/app/Firebase/).'
            );
        }
        // Relative path: try base_path first, then storage_path (canonical casing: Firebase)
        $candidates = [
            base_path($path),
            storage_path('app/Firebase/firebase_credentials.json'),
        ];
        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }
        throw new \InvalidArgumentException(
            'Firebase credentials file not found. Place firebase_credentials.json in app/Firebase/ or storage/app/Firebase/ (capital F), or set FIREBASE_CREDENTIALS in .env.'
        );
    }

    public function sendMessage($token, $notification)
    {
        // Fetch the OAuth 2.0 access token
        try {
            $tokenResponse = $this->googleClient->fetchAccessTokenWithAssertion();
            Logger::info('Access Token Response:', $tokenResponse);
            $accessToken = $tokenResponse['access_token'] ?? null;
            if (!$accessToken) {
                throw new ExpectedException('FIREBASE_ACCESS_TOKEN_NOT_FOUND', 'Failed to retrieve access token');
            }
        } catch (\Exception $e) {
            Logger::error(null, $e);
            throw new ExpectedException('FIREBASE_ACCESS_TOKEN_NOT_FOUND', $e->getMessage());
        }
        // Define the Firebase Cloud Messaging API v1 URL
        $fcmUrl = 'https://fcm.googleapis.com/v1/projects/newflutterpushnotifications/messages:send';
        // Prepare the payload
        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $notification['title'],
                    'body'  => $notification['body'],
                ],
                'data' => $notification['data'] ?? [],
            ],
        ];
        try {
            // Make the HTTP request to FCM API
            $response = $this->httpClient->post($fcmUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            \Log::error('Error sending FCM message: ' . $e->getMessage());
            return ['error' => 'Failed to send message'];
        }
    }

    /**
     * Check if the FCM sendMessage response indicates success.
     * Success: response contains top-level "name" (e.g. projects/.../messages/...).
     * Failure: response contains "error" or is missing "name".
     */
    public static function wasSuccessful($response): bool
    {
        if (! is_array($response)) {
            return false;
        }
        if (isset($response['error'])) {
            return false;
        }
        return isset($response['name']) && is_string($response['name']);
    }
}
