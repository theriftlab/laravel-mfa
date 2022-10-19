<?php

namespace Mfa\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Mfa\Contracts\MfaUser;

class MfaTriggered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public MfaUser $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MfaUser $user)
    {
        $this->user = $user;
    }
}
