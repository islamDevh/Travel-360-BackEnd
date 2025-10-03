<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\API\SocialAuthService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialAuthController extends BaseController
{
    public function __construct(protected SocialAuthService $SocialAuthService) {}
    public function social_login(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:google,apple',
            'id_token' => 'required|string',
        ]);

        $provider = $request->provider;
        $idToken  = $request->id_token;

        $userData = $this->SocialAuthService->verify($provider, $idToken);

        if (!$userData['success']) {
            return $this->errorResponse($userData['message']);
        }

        $user = User::firstOrCreate(
            ['email' => $userData['email']],
            [
                'name'     => $userData['name'] ?? $userData['email'],
                'password' => bcrypt(str()->random(16)),
                'provider' => $provider,
                'provider_id' => $userData['provider_id']
            ]
        );

        if (!$user->provider) {
            $user->update(['provider' => $provider]);
        }

        $token = JWTAuth::fromUser($user);

        $data  = ['user' => $user, 'token' => $token];
        return $this->successResponse($data, 'User logged in successfully');
    }
}
