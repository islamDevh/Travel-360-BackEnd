<?php

namespace App\Services;

use App\Mail\emails\EmailVerificationOTP;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class OTPService
{
    public function sendEmailOtp(User $user)
    {
        $cacheKey  = 'otp_send_limit_' . $user->id;
        $sendCount = Cache::get($cacheKey, 0);

        if ($sendCount >= 3) {
            abort(429, 'Too many OTP requests. Please try again after 3 minutes.');
        }

        $otp                  = rand(1000, 9999);
        $user->otp            = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(3);
        $user->save();

        Mail::to($user->email)->send(new EmailVerificationOTP($otp));

        Cache::put($cacheKey, $sendCount + 1, now()->addMinutes(3));
    }

    public function sendSmsOtp()
    {
        // TODO: integrate SMS provider
    }

    public function verifyOtp(string $otp, User $user)
    {
        $cacheKey = 'otp_attempts_' . $user->id;
        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= 5) {
            abort(429, 'Too many tries. Please try again later.');
        }

        if (!$user->otp || $user->otp_expires_at < now()) {
            abort(400, 'OTP has expired, please request a new one.');
        }

        if (trim($otp) !== (string) $user->otp) {
            Cache::put($cacheKey, $attempts + 1, now()->addMinutes(3));
            abort(400, 'Invalid OTP.');
        }

        if ($user->registered_by === 'email') {
            $user->email_verified_at = now();
        } else {
            $user->phone_verified_at = now();
        }

        $user->otp            = null;
        $user->otp_expires_at = null;
        $user->save();

        Cache::forget($cacheKey);
    }
}
