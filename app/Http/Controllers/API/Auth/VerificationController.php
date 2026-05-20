<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\VerifyOtpRequest;
use App\Services\AuthService;

class VerificationController extends BaseController
{
    public function __construct(protected AuthService $authService)
    {
    }

    public function verify_otp(VerifyOtpRequest $request)
    {
        $data = $this->authService->verifyOtp($request->validated());
        return $this->successResponse($data);
    }

    public function resend_otp()
    {
        $this->authService->resendOtp();
        return $this->successResponse(null);
    }
}
