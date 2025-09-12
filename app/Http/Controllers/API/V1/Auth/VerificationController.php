<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Services\API\OTPService;
use Illuminate\Http\Request;

class VerificationController extends BaseController
{
    public function __construct(protected OTPService $OTPService) {}

    public function resend_otp(Request $request)
    {
        $user = auth()->user();

        if ($user->registered_by == 'email') {
            $status = $this->OTPService->send_email_otp($user);
            if ($status['success']) {
                return $this->successResponse([], $status['message']);
            }
        }
        if ($user->registered_by == 'phone') {
            $status = $this->OTPService->send_SMS_OTP($user);
            if (!$status['success']) {
                return $this->successResponse([], $status['message']);
            }
        }

        return $this->errorResponse([], $status['message'],401);
    }

    public function verify_otp(Request $request)
    {
        $request->validate(['otp' => 'required|string']);
        $result = $this->OTPService->verify_OTP($request->otp);

        if ($result['success']) {
            return $this->successResponse(null, $result['message']);
        }

        return $this->errorResponse([], $result['message']);
    }
}
