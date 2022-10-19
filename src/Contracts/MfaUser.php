<?php

namespace Mfa\Contracts;

interface MfaUser
{
    public function generateMfaCode(): string;

    public function markMfaCodeAsUsed(): void;

    public function checkMfaCode(string $code): bool;
}
