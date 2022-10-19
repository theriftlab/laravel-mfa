<?php

namespace Mfa\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Mfa\Contracts\MfaUser;

class MfaLoginLink extends Mailable
{
    use Queueable, SerializesModels;

    public MfaUser $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MfaUser $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(config('app.name').' Secure Login')
            ->markdown('mfa::emails.login-link');
    }
}
