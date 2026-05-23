<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleService
{
    /**
     * Verify a Firebase ID token and return the user's profile data from Google.
     */
    public function fetchUser($idToken)
    {
        $apiKey = env('FIREBASE_API_KEY');

        $response = Http::post("https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=$apiKey", [
            "idToken" => $idToken
        ]);

        $data = $response->json();

        return [
            'email' => $data['users'][0]['email'],
            'name'  => $data['users'][0]['displayName'] ?? null,
            'id'    => $data['users'][0]['localId'],
            'image' => $data['users'][0]['photoUrl'] ?? null,
        ];
    }
}
