<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleService
{
    public function fetchUser($token)
    {
        $response = Http::get('https://www.googleapis.com/oauth2/v3/userinfo', [
            'access_token' => $token,
        ])->json();

        return [
            'id'     => $response['sub'] ?? null,
            'name'   => $response['name'] ?? null,
            'email'  => $response['email'] ?? null,
            'image' => $response['picture'] ?? null,
        ];
    }
}
