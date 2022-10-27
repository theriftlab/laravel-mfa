<?php

namespace Mfa\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Mfa\Contracts\MfaUser;
use Mfa\Models\MfaCode;

trait Mfa
{
    /**
     * Get the MFA code for this user.
     */
    public function mfaCode(): Relation
    {
        return $this->hasOne(MfaCode::class);
    }

    /**
     * Find a user instance with a matching MFA code.
     */
    public function scopeWithMfaCode($query, $code): Builder
    {
        return $query->whereRelation('mfaCode', 'code', $code);
    }

    /**
     * Save & return a new MFA code.
     */
    public function generateMfaCode(): string
    {
        $code = Str::random(config('mfa.code_length'));

        $this->mfaCode()->withoutGlobalScope('valid')->updateOrCreate(
            [
                'user_id' => $this->id,
            ],
            [
                'code' => $code,
                'used_at' => null,
            ]
        );

        return $code;
    }

    /**
     * Mark this user's code as used & invalid.
     */
    public function markMfaCodeAsUsed(): void
    {
        $this->mfaCode?->update([
            'used_at' => now(),
        ]);
    }

    /**
     * Authenticate a user's MFA code.
     */
    public function checkMfaCode(string $code): bool
    {
        return $this->mfaCode?->code === $code;
    }

    /**
     * The "booted" method of the model.
     * Delete the MFA code record on user deletion.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleted(function (MfaUser $user) {
            $user->mfaCode?->delete();
        });
    }
}
