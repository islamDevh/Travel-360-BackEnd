<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\ForgotPasswordRequest;
use App\Http\Requests\API\ResetPasswordRequest;
use App\Services\AuthService;

class ResetPasswordController extends BaseController
{
    public function __construct(protected AuthService $authService)
    {
    }

    /**
     * Send a password reset OTP to the user.
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->authService->forgotPassword($request->validated());
        return $this->successResponse(null);
    }

    /**
     * Reset the user's password using a verified OTP.
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword($request->validated());
        return $this->successResponse(null);
    }
}
