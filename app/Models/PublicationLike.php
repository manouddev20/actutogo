<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicationLike extends Model
{
    protected $fillable = [
        'publication_id',
        'user_id',
        'visitor_token',
    ];

    /**
     * Publication likée.
     */
    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    /**
     * Utilisateur connecté ayant liké.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}