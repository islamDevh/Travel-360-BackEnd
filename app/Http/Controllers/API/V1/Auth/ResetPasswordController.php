<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\BaseController;
use App\Models\User;
use App\Services\OTPService;
use Illuminate\Http\Request;

class ResetPasswordController extends BaseController
{
    public function __construct(protected OTPService $OTPService) {}
    /**
     * Handle forgot password: SMS/Email OTP
     */
    public function forgot_password(Request $request)
    {
        $request->validate([
            'registered_by' => 'required|in:email,phone',
            'email' => 'nullable|string|required_if:registered_by,email',
            'phone' => 'nullable|string|max:15|required_if:registered_by,phone',
        ]);

        if ($request->registered_by == 'email') {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->notFoundResponse('User not found');
            }
            $status = $this->OTPService->send_email_otp($user);
            if ($status['success']) {
                return $this->successResponse(null, $status['message']);
            }
        } else {
            $user = User::where('phone', $request->phone)->first();
            if (!$user) {
                return $this->notFoundResponse('User not found');
            }
            $status = $this->OTPService->send_SMS_OTP($user);
            if ($status['success']) {
                return $this->successResponse(null, $status['message']);
            }
        }

        return $this->errorResponse(null, $status['message']);
    }

    /**
     * Handle reset password: verify OTP and reset password
     */

    public function reset_password(Request $request)
    {
        $request->validate(['password' => 'required|string|min:6|confirmed']);

        $user = auth()->user();
        $user->password = $request->password;
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();
        return $this->successResponse(null, 'Password reset successfully');
    }
}
