<?php


namespace App\Services\API;

use Illuminate\Support\Facades\Http;

class SocialAuthService
{
    public function verify(string $provider, string $token): ?array
    {
        switch ($provider) {
            case 'google':
                return $this->verifyGoogle($token);
            case 'facebook':
                return $this->verifyFacebook($token);
            case 'apple':
                return $this->verifyApple($token);
            default:
                return ['success' => false, 'message' => 'Invalid provider'];
        }
    }

    private function verifyGoogle(string $idToken): ?array
    {
        $googleResponse = Http::get("https://oauth2.googleapis.com/tokeninfo", ['id_token' => $idToken,]);

        if ($googleResponse->failed()) {
            return [
                'success' => false,
                'message' => $googleResponse->body(),
            ];
        }

        $data = $googleResponse->json();

        $clientId = config('services.socialite.google.client_id');
        if (!isset($data['aud']) || $data['aud'] !== $clientId) {
            return [
                'success' => false,
                'message' => 'Invalid token',
            ];
        }

        return [
            'success' => true,
            'email' => $data['email'] ?? null,
            'name'  => $data['name'] ?? null,
            'provider_id' => $data['sub'] ?? null
        ];
    }

    private function verifyFacebook(string $accessToken): ?array
    {
        $appId = "FACEBOOK_APP_ID";
        $appSecret = "FACEBOOK_APP_SECRET";

        $response = Http::get("https://graph.facebook.com/debug_token", [
            'input_token'  => $accessToken,
            'access_token' => "$appId|$appSecret"
        ]);

        if ($response->failed()) {
            return null;
        }

        $debug = $response->json();
        if (!isset($debug['data']['is_valid']) || !$debug['data']['is_valid']) {
            return null;
        }

        // Get user info
        $userResponse = Http::get("https://graph.facebook.com/me", [
            'fields'       => 'id,name,email',
            'access_token' => $accessToken,
        ]);

        if ($userResponse->failed()) {
            return null;
        }

        $user = $userResponse->json();

        return [
            'email' => $user['email'] ?? null,
            'name'  => $user['name'] ?? null,
        ];
    }

    private function verifyApple(string $idToken): ?array
    {
        // TODO: Implement Apple JWT verification using Apple public keys
        return null;
    }
}
