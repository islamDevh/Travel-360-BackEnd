<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\JWK;

use Illuminate\Support\Facades\Http;

class AppleService
{
    public function fetchUser($token)
    {
        $tks = explode('.', $token);
        if (count($tks) != 3) {
            throw new \Exception('Invalid token format');
        }

        list($headb64, $bodyb64, $cryptob64) = $tks;
        $header = json_decode(JWT::urlsafeB64Decode($headb64), true);

        $response = Http::get('https://appleid.apple.com/auth/keys');


        
        $keys = $response->json()['keys'];

        // Convert keys to JWK format
        $jwks = ['keys' => $keys];

        // Decode and verify the token
        $decoded = JWT::decode($token, JWK::parseKeySet($jwks), ['RS256']);

        // Verify the issuer
        if ($decoded->iss !== 'https://appleid.apple.com') {
            throw new \Exception('Invalid token issuer');
        }

        // Verify audience (your app's client ID)
        if ($decoded->aud !== config('services.socialite.apple.client_id')) {
            throw new \Exception('Invalid token audience');
        }

        return [
            'id' => $decoded->sub,
            'email' => $decoded->email ?? null,
            'name' => null, // Apple doesn't provide name in token, must be sent separately by client
            'image' => null,
        ];
    }
}
