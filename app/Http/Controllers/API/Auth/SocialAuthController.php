<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Services\SocialAuthService;
use Illuminate\Http\Request;

class SocialAuthController extends BaseController
{
    public function __construct(protected SocialAuthService $socialAuthService)
    {
    }

    /**
     * Authenticate a user via a third-party social provider.
     */
    public function socialLogin(Request $request, string $provider)
    {
        $request->validate(['token' => 'required|string']);

        $data = $this->socialAuthService->socialLogin($request->token, $provider);
        return $this->successResponse($data);
    }
}
