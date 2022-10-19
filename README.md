# Basic Laravel MFA

## Overview

This is a bare-bones email-based 2FA package which can be configured to send out an email containing a signed link upon successful authentication. Any routes you place under the provided `mfa` middleware will be inaccessible until the link is clicked.

## Installation

```bash
composer require theriftlab/laravel-mfa
```

Optionally, publish the migration:

```bash
php artisan vendor:publish --tag=mfa-migrations
```

Then:

```bash
php artisan migrate
```

## Setup

### Add to User Model

First, you will need to mark your `User` model (or whatever model you are using for Auth) as ready for MFA:

```diff
+use Mfa\Contracts\MfaUser;
+use Mfa\Concerns\Mfa;
...

-class User extends Authenticatable
+class User extends Authenticatable implements MfaUser
{
    use HasApiTokens;
    use HasFactory;
+   use Mfa;
    use Notifiable;
    ...
}
```

### Add to Auth Flow

Due to the non-standard nature of Laravel's auth/login flow, it is up to you to decide where/when to trigger & end the MFA session using the `MfaAuth` facade, which expects an authenticated user to be present in order to work.

For example, in a [Breeze](https://github.com/laravel/breeze) setup, you might add these lines into `app/Http/Controllers/Auth/AuthenticatedSessionController`:

```diff
use Mfa\Facades\MfaAuth;

...

    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

+       if (MfaAuth::isActive()) {
+           MfaAuth::trigger();
+           return redirect()->route('mfa.sent');
+       }

        ...
    }

...

    public function destroy(Request $request)
    {
+       if (MfaAuth::isActive()) {
+           MfaAuth::logout();
+       }

        Auth::guard('web')->logout();
        ...
    }
```

### Configure & Add Views

The email containing the signed link is a very simple template, and can be published:

```bash
php artisan vendor:publish --tag=mfa-views
```

There are also two view files which you will need to implement: `resources/views/auth/mfa-sent.blade.php` and `resources/views/auth/mfa-invalid.blade.php`.

* `mfa-sent.blade.php` is shown when the user is first authorized by Laravel's default auth process and is waiting for the MFA signed link email. This template can optionally contain a link / button to POST to named route `mfa.resend`, which will resend the signed link email. The `$errors` session data will contain an error message if an invalid link is clicked, and `session('status')` will contain a message if the link email is resent. A logout link is also a good idea on this page to restart the whole process, in case the wrong account is logged in.

* `mfa-invalid.blade.php` is shown when the user is *not* authorized and an invalid link is clicked, and therefore any resend / logout options are not available.

**Note:** when the user is *not* authorized and a *valid* link is clicked from an email (eg. the initial default auth session might have timed out), the user will be automatically logged in.

### Configuring Your Routes

Finally, on whichever routes you wish to protect with MFA, you can add the `mfa` middleware after `auth` - for example:

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'mfa'])->name('dashboard');
```

This will redirect any `Auth`ed but un`MFA`ed user back to display your `auth.mfa-sent` view.

## Configuration

The default config is fairly self-explanatory and looks like this:

```php
// Whether MFA is active
'active' => env('MFA_ACTIVE', true),

// How many minutes the signed link lasts before timing out
'link_timeout' => env('MFA_LINK_TIMEOUT', 60),

// How many chars long the generated code should be
'code_length' => env('MFA_CODE_LENGTH', 32),

// URL to redirect to when link has been authorized
'redirect_url' => env('MFA_REDIRECT_URL', '/'),

// Which model will be adopting the MfaUser functionality
'model' => env('MFA_MODEL', 'App\Models\User'),
```

You may publish the config file if you wish to change the defaults:

```bash
php artisan vendor:publish --tag=mfa-config
```