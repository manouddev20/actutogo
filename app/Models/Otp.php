<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'email',
    'otp',
    'type',
    'status',
    'attempts',
    'expires_at',
    'verified_at',
])]

class Otp extends Model
{
    use SoftDeletes;

    /**
     * Un OTP peut appartenir à un utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vérifie si l'OTP est expiré.
     */
    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    /**
     * Vérifie si l'OTP est validé.
     */
    public function isVerified(): bool
    {
        return $this->status === 1;
    }
}