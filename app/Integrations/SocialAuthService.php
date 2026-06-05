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

    /**
     * Authenticate via a social provider token and return a JWT token.
     */
    public function socialLogin(string $token, string $provider)
    {
        $socialUser = $this->fetchUserFromProvider($provider, $token);
        $user       = $this->firstOrCreateUser($socialUser, $provider);

        return [
            'token' => JWTAuth::fromUser($user),
            'user'  => new UserResource($user),
        ];
    }

    /**
     * Delegate token verification to the correct provider service.
     */
    private function fetchUserFromProvider(string $provider, string $token): array
    {
        return match ($provider) {
            'google'   => $this->GoogleService->fetchUser($token),
            'apple'    => $this->AppleService->fetchUser($token),
            'facebook' => $this->FacebookService->fetchUser($token),
            default    => abort(400, 'Invalid provider.'),
        };
    }

    /**
     * Find an existing user by provider ID or email, or create a new one.
     */
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
