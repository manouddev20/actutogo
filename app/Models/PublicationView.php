<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'publication_id',
    'user_id',
    'visitor_token',
])]

class PublicationView extends Model
{
    /**
     * La publication qui a été consultée.
     */
    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    /**
     * L'utilisateur connecté qui a consulté la publication.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}