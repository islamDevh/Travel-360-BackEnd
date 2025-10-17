<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FacebookService
{
    public function fetchUser($token)
    {
        $response = Http::get('https://graph.facebook.com/me', [
            'fields'       => 'id,name,email,picture',
            'access_token' => $token,
        ])->json();
            dd($response);
        return [
            'id'     => $response['id'] ?? null,
            'name'   => $response['name'] ?? null,
            'email'  => $response['email'] ?? null,
            'avatar' => $response['picture']['data']['url'] ?? null,
        ];
    }
}
