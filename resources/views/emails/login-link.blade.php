@component('mail::message')
# {{ config('app.name') }} Secure Login

Click the button below to securely sign in. This link
@if (config('mfa.link_single_use'))
    can only be used once and
@endif
will expire after {{ config('mfa.link_timeout') }} minutes if unused.

@component('mail::button', ['url' => $user->mfaCode->url])
Sign In
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
