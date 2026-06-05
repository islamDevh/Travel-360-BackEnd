<?php

namespace App\Actions;

class SendSmsAction
{
    public function execute(string $phone, string $message): void
    {
        // TODO: integrate your SMS provider (e.g. Twilio, Vonage) and send $message to $phone
    }
}
