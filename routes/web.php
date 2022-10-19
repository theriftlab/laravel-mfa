<?php

use Illuminate\Support\Facades\Route;
use Mfa\Http\Controllers\Auth\MfaLinkController;

Route::middleware('web')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('mfa-sent', [MfaLinkController::class, 'sent'])
            ->name('mfa.sent');

        Route::post('mfa-resend', [MfaLinkController::class, 'resend'])
            ->name('mfa.resend');
    });

    Route::get('mfa-invalid', [MfaLinkController::class, 'invalid'])
        ->name('mfa.invalid');

    Route::get('authorize-login/{code}', [MfaLinkController::class, 'authenticate'])
        ->where('code', '[a-zA-Z0-9]{'.config('mfa.code_length').'}')
        ->name('mfa.authenticate');
});
