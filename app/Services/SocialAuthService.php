<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class SocialAuthService
{
    protected $providers;

    public function __construct($providers)
    {
        $this->providers = $providers;
    }

    public function fetchUserFromProvider($provider, $token)
    {
        if (!isset($this->providers[$provider])) {
            throw new \Exception('Provider ' . $provider . ' is not supported.');
        }
        
        $service = $this->providers[$provider];
        return $service->fetchUser($token);
    }

    public function firstOrCreateUser($socialUser, $provider)
    {
        $user = User::where('social_id', $socialUser['id'])
            ->where('provider', $provider)
            ->first();

        if ($user) {
            return $user;
        }

        if (!empty($socialUser['email'])) {
            $user = User::where('email', $socialUser['email'])->first();

            if ($user) {
                $user->update([
                    'social_id' => $socialUser['id'],
                    'provider'  => $provider,
                ]);
                return $user;
            }
        }

        return User::create([
            'name' => $socialUser['name'] ?? 'User',
            'email' => $socialUser['email'] ?? null,
            'social_id' => $socialUser['id'],
            'provider' => $provider,
            'email_verified_at' => !empty($socialUser['email']) ? now() : null,
            'password' => bcrypt(Str::random(16)),
        ]);
    }
}
