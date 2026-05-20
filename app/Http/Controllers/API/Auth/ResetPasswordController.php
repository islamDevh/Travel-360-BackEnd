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

    public function forgot_password(ForgotPasswordRequest $request)
    {
        $this->authService->forgotPassword($request->validated());
        return $this->successResponse(null);
    }

    public function reset_password(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword($request->validated());
        return $this->successResponse(null);
    }
}
