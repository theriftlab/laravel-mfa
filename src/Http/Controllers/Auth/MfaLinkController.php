<?php

namespace Mfa\Http\Controllers\Auth;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Mfa\Facades\MfaAuth;

class MfaLinkController extends BaseController
{
    /**
     * Show the waiting page.
     *
     * @return \Illuminate\Http\Response
     */
    public function sent()
    {
        if (MfaAuth::check()) {
            return redirect(config('mfa.redirect_url'));
        }

        return view('auth.mfa-sent');
    }

    public function invalid()
    {
        return view('auth.mfa-invalid');
    }

    /**
     * Authenticate a link.
     *
     * @param string $code
     * @return \Illuminate\Http\Response
     */
    public function authenticate(string $code)
    {
        // Log user in from signed link if not currently logged in
        $user = Auth::user();

        if (! $user) {
            $user = config('mfa.model')::withMfaCode($code)->first();

            if (! is_null($user)) {
                Auth::login($user);
            }
        }

        if (Auth::check()) {
            if (MfaAuth::attempt($code)) {
                return redirect()->intended(config('mfa.redirect_url'));
            } else {
                return redirect()->route('mfa.sent')->withErrors([
                    'invalid' => 'Invalid link.',
                ]);
            }
        }

        return redirect()->route('mfa.invalid');
    }

    /**
     * Resend the MFA code.
     *
     * @return \Illuminate\Http\Response
     */
    public function resend()
    {
        MfaAuth::trigger();

        return redirect()->route('mfa.sent')->with([
            'status' => 'Another link has been emailed to you.',
        ]);
    }
}
