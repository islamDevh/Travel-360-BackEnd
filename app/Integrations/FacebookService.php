<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FacebookService
{
    /**
     * Fetch the user's profile data from the Facebook Graph API using an access token.
     */
    public function fetchUser($token)
    {
        $response = Http::get('https://graph.facebook.com/me', [
            'fields'       => 'id,name,email,picture',
            'access_token' => $token,
        ])->json();

        return [
            'id'     => $response['id'] ?? null,
            'name'   => $response['name'] ?? null,
            'email'  => $response['email'] ?? null,
            'image' => $response['picture']['data']['url'] ?? null,
        ];
    }
}
