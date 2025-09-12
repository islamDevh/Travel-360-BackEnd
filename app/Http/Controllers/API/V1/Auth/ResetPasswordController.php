<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\API\EmailService;
use Illuminate\Http\Request;

class ResetPasswordController extends BaseController
{
    public function __construct(protected EmailService $emailService) {}

    public function send_otp_forgot_password(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->errorResponse(null, 'User not found');
        }

        $this->emailService->sendEmailOtp($user);
        
        return $this->successResponse(null, 'OTP sent successfully to your email');
    }

    public function reset_password_by_otp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->errorResponse(null, 'User not found');
        }

        // check otp is valid
        $otpRecord = User::where('id', $user->id)
            ->where('otp', $request->otp)
            ->where('otp_expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return $this->errorResponse(null, 'Invalid OTP or OTP has expired');
        }

        $user->password = $request->new_password;
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();


        return response()->json(['success' => true, 'message' => 'Password reset successfully']);
    }
}
