<?php

namespace Mfa\Facades;

use Illuminate\Support\Facades\Facade;

class MfaAuth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return \Mfa\Services\MfaAuth::class;
    }
}
