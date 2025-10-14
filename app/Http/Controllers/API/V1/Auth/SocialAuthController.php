<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\BaseController;
use App\Services\SocialAuthService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialAuthController extends BaseController
{

    public function __construct(protected SocialAuthService $socialAuthService) {}

    public function social_login(request $request, $provider)
    {
        try {
            $validated = $request->validate(['token' => 'required|string']);

            $socialUserData = $this->socialAuthService->fetchUserFromProvider($provider, $validated['token']);

            $data['user'] = $this->socialAuthService->firstOrCreateUser($socialUserData, $provider);

            $data['token'] = JWTAuth::fromUser($data['user']);

            return $this->successResponse($data, 'Login successful', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
