<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChanged extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $updated_at;
    protected string $lang;

    public function __construct($updated_at, string $lang = "en")
    {
        $this->updated_at = $updated_at;
        $this->lang = $lang;
        $this->locale($this->lang)
            ->subject(__('email.subject.password_changed', [], $this->lang));
    }

    public function build()
    {
        return $this
            ->view('emails.password_changed')
            ->text('emails.password_changed_plain')
            ->with([
                'updated_at' => $this->updated_at
            ]);
    }
}
