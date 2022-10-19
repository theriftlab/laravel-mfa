<?php

namespace Mfa\Services;

use Mfa\Events\MfaTriggered;
use Mfa\Contracts\MfaUser;

class MfaAuth
{
    protected MfaUser $user;

    public function __construct(MfaUser $user)
    {
        $this->user = $user;
    }

    /**
     * Helper to determine if MFA is switched on or off.
     */
    public function isActive()
    {
        return config('mfa.active');
    }

    /**
     * Simple function to begin the process & emit event.
     */
    public function trigger()
    {
        $this->generateCode();
        MfaTriggered::dispatch($this->user);
    }

    /**
     * Generate a new MFA code for this user.
     */
    public function generateCode()
    {
        $this->user->generateMfaCode();
    }

    /**
     * Mark the user's MFA code as used.
     */
    public function invalidateCode()
    {
        $this->user->markMfaCodeAsUsed();
    }

    /**
     * Attempt to authenticate the user's MFA code.
     */
    public function attempt(string $code): bool
    {
        if (! $this->user->checkMfaCode($code)) {
            return false;
        }

        $this->invalidateCode();
        session()->put('auth.mfa_authenticated', $this->user->getKey());

        return true;
    }

    /**
     * Deauthenticate the user's MFA code.
     */
    public function logout()
    {
        session()->forget('auth.mfa_authenticated');
    }

    /**
     * Check whether the user's MFA code is authenticated.
     */
    public function check(): bool
    {
        return session()->has('auth.mfa_authenticated') && session()->get('auth.mfa_authenticated') === $this->user->getKey();
    }
}
