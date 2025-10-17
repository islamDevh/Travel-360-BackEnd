<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class SocialAuthService
{
    public function __construct(
        protected GoogleService $GoogleService,
        protected AppleService $AppleService,
        protected FacebookService $FacebookService
    ) {}

    public function fetchUserFromProvider($provider, $token)
    {
        switch ($provider) {
            case 'google':
                return $this->GoogleService->fetchUser($token);
            case 'apple':
                return $this->AppleService->fetchUser($token);
            case 'facebook':
                return $this->FacebookService->fetchUser($token);
            default:
                throw new \Exception('Invalid provider');
        }
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
