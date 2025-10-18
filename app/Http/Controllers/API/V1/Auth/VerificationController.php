<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OTPService;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

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

        return $this->errorResponse($status['message'], 401);
    }

    public function verify_otp(Request $request)
    {
        $request->validate(['otp' => 'required|string', 'user_id' => 'required|integer|exists:users,id']);

        $data['user'] = User::findOrFail($request->user_id);

        $result = $this->OTPService->verify_OTP($request->otp, $data['user']);

        if ($result['success']) {
            $data['token'] = JWTAuth::fromUser($data['user']);
            return $this->successResponse($data, $result['message']);
        }

        return $this->errorResponse($result['message']);
    }
}
