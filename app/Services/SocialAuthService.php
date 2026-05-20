<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class SocialAuthService
{
    public function __construct(
        protected GoogleService $GoogleService,
        protected AppleService $AppleService,
        protected FacebookService $FacebookService
    ) {
    }

    public function socialLogin(string $token, string $provider): array
    {
        $socialUser = $this->fetchUserFromProvider($provider, $token);
        $user       = $this->firstOrCreateUser($socialUser, $provider);

        return [
            'token' => JWTAuth::fromUser($user),
            'user'  => new UserResource($user),
        ];
    }

    private function fetchUserFromProvider(string $provider, string $token): array
    {
        return match ($provider) {
            'google'   => $this->GoogleService->fetchUser($token),
            'apple'    => $this->AppleService->fetchUser($token),
            'facebook' => $this->FacebookService->fetchUser($token),
            default    => abort(400, 'Invalid provider.'),
        };
    }

    private function firstOrCreateUser(array $socialUser, string $provider): User
    {
        $user = User::where('provider_id', $socialUser['id'])
            ->where('provider', $provider)
            ->first();

        if ($user) {
            return $user;
        }

        if (!empty($socialUser['email'])) {
            $user = User::where('email', $socialUser['email'])->first();

            if ($user) {
                $user->update([
                    'provider_id' => $socialUser['id'],
                    'provider'    => $provider,
                ]);
                return $user;
            }
        }

        return User::create([
            'first_name'        => $socialUser['first_name'] ?? 'User',
            'last_name'         => $socialUser['last_name'] ?? '',
            'full_name'         => $socialUser['name'] ?? 'User',
            'email'             => $socialUser['email'] ?? null,
            'provider_id'       => $socialUser['id'],
            'provider'          => $provider,
            'registered_by'     => 'email',
            'email_verified_at' => !empty($socialUser['email']) ? now() : null,
            'password'          => Str::random(16),
        ]);
    }
}
