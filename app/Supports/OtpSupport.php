<?php

namespace App\Supports;

use App\Actions\SendEmailAction;
use App\Actions\SendSmsAction;
use Ichtrojan\Otp\Otp;

class OtpSupport
{
    public function send(string $via, string $identifier, int $length = 4, int $validity = 5): string
    {
        $result = (new Otp())->generate($identifier, 'numeric', $length, $validity);
        $code = $result->token;
        $title = 'OTP Code';
        $message = 'Your OTP code is: ' . $code . '. It is valid for ' . $validity . ' minutes.';
        
        if ($via === 'email') {
            app(SendEmailAction::class)->execute($identifier, $title, $message);
        } elseif ($via === 'sms') {
            app(SendSmsAction::class)->execute($identifier, $message);
        } else {
            abort(400, "Unsupported OTP channel: $via");
        }

        return $code;
    }

    public function validate(string $identifier, string $otp): bool
    {
        $result = (new Otp())->validate($identifier, $otp);

        if (!$result->status) {
            abort(400, $result->message);
        }

        return true;
    }
}
