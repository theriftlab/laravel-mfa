<?php

namespace Mfa\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MfaCode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'used_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the code.
     */
    public function user()
    {
        return $this->belongsTo(config('mfa.model'));
    }

    /**
     * Generate a link with this code.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => route('mfa.authenticate', ['code' => $this->code])
        );
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Ensure this MFA code is unused and unexpired
        static::addGlobalScope('valid', function (Builder $builder) {
            $builder
                ->whereNull('used_at')
                ->where('updated_at', '>', now()->subMinutes(config('mfa.link_timeout')));
        });
    }
}
