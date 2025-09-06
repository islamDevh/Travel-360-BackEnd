<?php


namespace App\Services\API;

use App\Mail\emails\EmailVerificationOTP;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class OTP
{
    public function sendEmailOtp($user)
    {
        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new EmailVerificationOTP($otp));
    }

    public function verifyOtp($otpInput)
    {
        $user = Auth::user();
        
        if ($user->email_verified_at) {
            return [
                'success' => true,
                'message' => 'Email is already verified'
            ];
        }

        $cacheKey = 'otp_attempts_' . $user->id;
        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= 5) {
            return [
                'success' => false,
                'message' => 'Too many tries. Please try again later.'
            ];
        }

        if (!$user->otp || $user->otp_expires_at < now()) {
            return [
                'success' => false,
                'message' => 'OTP has expired, please request a new one'
            ];
        }

        if (trim($otpInput) !== (string)$user->otp) {
            // increment attempt counter
            Cache::put($cacheKey, $attempts + 1, now()->addMinutes(10));

            return [
                'success' => false,
                'message' => 'Invalid OTP'
            ];
        }

        // Success: verify email and reset OTP
        $user->email_verified_at = now();
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        Cache::forget($cacheKey);

        return [
            'success' => true,
            'message' => 'Email verified successfully'
        ];
    }
}
