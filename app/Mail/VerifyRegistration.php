<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class VerifyRegistration extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this
            ->view('emails.verify_registration')
            ->text('emails.verify_registration_plain')
            ->with([
                'name' => $this->user->name,
                'verification_token' => $this->user->verification_token,
                'lang' => $this->user->language
            ]);
    }
}
