<?php

namespace Mfa\Listeners;

use Illuminate\Support\Facades\Mail;
use Mfa\Events\MfaTriggered;
use Mfa\Mail\MfaLoginLink;

class SendMfaLink
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\MfaTriggered  $event
     * @return void
     */
    public function handle(MfaTriggered $event)
    {
        Mail::to($event->user)->send(new MfaLoginLink($event->user));
    }
}
