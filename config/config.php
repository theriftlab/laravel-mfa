<?php

return [
    // Whether MFA is active
    'active' => env('MFA_ACTIVE', true),

    // How many minutes the signed link lasts before timing out
    'link_timeout' => env('MFA_LINK_TIMEOUT', 60),

    // Whether using the link invalidates it
    'link_single_use' => env('MFA_LINK_SINGLE_USE', true),

    // How many chars long the generated code should be
    'code_length' => env('MFA_CODE_LENGTH', 32),

    // URL to redirect to when link has been authorized
    'redirect_url' => env('MFA_REDIRECT_URL', '/'),

    // Which model will be adopting the MfaUser functionality
    'model' => env('MFA_MODEL', 'App\Models\User'),
];
