<?php

namespace App\Actions;

use App\Mail\GeneralEmail;
use Illuminate\Support\Facades\Mail;

class SendEmailAction
{
    public function execute(string $email, string $title, string $message): void
    {
        Mail::to($email)->send(new GeneralEmail($title, $message));
    }
}
