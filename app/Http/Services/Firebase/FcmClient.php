<?php
namespace App\Http\Services\Firebase;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Log;

class FcmClient
{
    private string $credentialsPath;

    private HttpClient $httpClient;

    public function __construct()
    {
        $this->credentialsPath = $this->resolveCredentialsPath();
        $this->httpClient = new HttpClient();
    }

    /**
     * Resolve Firebase credentials path from config (FIREBASE_CREDENTIALS / GOOGLE_APPLICATION_CREDENTIALS).
     * Tries base_path, then storage_path, and supports both app/Firebase/ and app/Firebases/.
     */
    private function resolveCredentialsPath(): string
    {
        $defaultProject = config('firebase.default', 'app');
        $credentials = config("firebase.projects.{$defaultProject}.credentials", 'app/Firebase/firebase_credentials.txt');
        if (is_array($credentials)) {
            $credentials = $credentials['file'] ?? $credentials['path'] ?? 'app/Firebase/firebase_credentials.txt';
        }
        $path = is_string($credentials) ? $credentials : 'app/Firebase/firebase_credentials.txt';
        // Absolute path from env
        if (str_starts_with($path, '/') || (strlen($path) >= 2 && $path[1] === ':')) {
            if (is_file($path)) {
                return $path;
            }
            throw new \InvalidArgumentException('Firebase credentials file not found: ' . $path);
        }
        $candidates = [
            base_path($path),
            base_path('app/Firebase/firebase_credentials.txt'),
            base_path('app/Firebases/firebase_credentials.txt'),
            storage_path('app/Firebase/firebase_credentials.json'),
            storage_path('app/Firebase/firebase_credentials.txt'),
        ];
        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }
        throw new \InvalidArgumentException(
            'Firebase credentials file not found. Set FIREBASE_CREDENTIALS in .env or place credentials in app/Firebase/ or storage/app/Firebase/.'
        );
    }

    /**
     * Resolve Firebase project ID for FCM URL (config FIREBASE_PROJECT_ID or credentials file).
     */
    private function resolveProjectId(): string
    {
        $defaultProject = config('firebase.default', 'app');
        $projectId = config("firebase.projects.{$defaultProject}.project_id");
        if (! empty($projectId)) {
            return $projectId;
        }
        $json = json_decode((string) file_get_contents($this->credentialsPath), true);
        if (! empty($json['project_id'])) {
            return $json['project_id'];
        }
        throw new \InvalidArgumentException('Firebase project_id not found. Set FIREBASE_PROJECT_ID in .env or use a credentials file that contains project_id.');
    }

    public function sendMessage($token, $notification)
    {
        // OAuth2 scope (not audience) so we get access_token; id_token is rejected by FCM with 401
        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/firebase.messaging',
            $this->credentialsPath
        );
        try {
            $tokenResponse = $credentials->fetchAuthToken();
            $accessToken = $tokenResponse['access_token'] ?? null;
            if (! $accessToken) {
                Log::error('FCM: no access_token in token response', $tokenResponse);
                return ['error' => 'Failed to retrieve access token'];
            }
        } catch (\Exception $e) {
            Log::error('FCM: error fetching access token: ' . $e->getMessage());
            return ['error' => 'Could not fetch access token'];
        }

        $projectId = $this->resolveProjectId();
        $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

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
            $response = $this->httpClient->post($fcmUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error sending FCM message: ' . $e->getMessage());
            return ['error' => 'Failed to send message'];
        }
    }

    /**
     * Check if the FCM sendMessage response indicates success.
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
