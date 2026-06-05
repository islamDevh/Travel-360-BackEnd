<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneralEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $title;
    public string $data;

    public function __construct(string $title, string $data)
    {
        $this->title = $title;
        $this->data  = $data;
    }

    public function build(): self
    {
        return $this->subject($this->title)
            ->view('emails.general');
    }
}
