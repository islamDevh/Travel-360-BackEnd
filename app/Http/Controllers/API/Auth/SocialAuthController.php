<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Services\SocialAuthService;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class SocialAuthController extends BaseController
{
    public function __construct(protected SocialAuthService $socialAuthService)
    {
    }

    public function social_login(Request $request, string $provider)
    {
        $request->validate(['token' => 'required|string']);

        $data = $this->socialAuthService->socialLogin($request->token, $provider);
        return $this->successResponse($data);
    }
}
