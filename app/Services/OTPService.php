<?php


namespace App\Services;

use App\Mail\emails\EmailVerificationOTP;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class OTPService
{
    public function send_email_otp($user)
    {
        $cacheKey = 'otp_send_limit_' . $user->id;
        $sendCount = Cache::get($cacheKey, 0);

        if ($sendCount >= 3) {
            return [
                'success' => false,
                'message' => 'Too many OTP requests. Please try resend again later after 10 minutes.',
            ];
        }

        // generate OTP
        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new EmailVerificationOTP($otp));

        // increment counter with expiry
        Cache::put($cacheKey, $sendCount + 1, now()->addMinutes(10));

        return [
            'success' => true,
            'message' => 'OTP sent successfully'
        ];
    }



    public function verify_OTP($otpInput, $user)
    {
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
            Cache::put($cacheKey, $attempts + 1, now()->addMinutes(10));

            return [
                'success' => false,
                'message' => 'Invalid OTP'
            ];
        }

        // mark verified
        if ($user->registered_by === 'email') {
            $user->email_verified_at = now();
        }
        if ($user->registered_by === 'phone') {
            $user->phone_verified_at = now();
        }

        // reset OTP
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        Cache::forget($cacheKey);

        return [
            'success' => true,
            'message' => 'User verified successfully'
        ];
    }

    public function send_SMS_OTP($user)
    {
        return [
            'success' => true,
        ];
    }
}
