<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'first_name',
    'last_name',
    'email',
    'phone',
    'slug',
    'description',
    'photo',
    'status',
])]

class Author extends Model
{
    use SoftDeletes;

    /**
     * Utilisateur qui a créé cet auteur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }
}