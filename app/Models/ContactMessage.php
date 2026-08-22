<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'first_name',
    'last_name',
    'email',
    'phone',
    'subject',
    'title',
    'message',
    'status',
])]

class ContactMessage extends Model
{
    use SoftDeletes;

    /**
     * Un message peut appartenir à un utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}