<?php

namespace Mfa\Http\Middleware;

use Closure;
use Mfa\Facades\MfaAuth;
use Illuminate\Http\Request;

class Mfa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('mfa.active') && ! MfaAuth::check()) {
            return redirect()->route('mfa.sent');
        }

        return $next($request);
    }
}
