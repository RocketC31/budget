<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected string $token;
    protected string $lang;

    public function __construct(string $token, string $lang = "en")
    {
        $this->token = $token;
        $this->lang = $lang;
        $this->locale($this->lang)
            ->subject(__('email.subject.reset_password', [], $this->lang));
    }

    public function build()
    {
        return $this
            ->view('emails.reset_password')
            ->text('emails.reset_password_plain')
            ->with([
                'token' => $this->token
            ]);
    }
}
