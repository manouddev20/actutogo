<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'comment_id',
    'user_id',
    'visitor_token',
    'reason',
    'description',
])]

class CommentReport extends Model
{
    /**
     * Le commentaire signalé.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * L'utilisateur connecté ayant signalé le commentaire.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}