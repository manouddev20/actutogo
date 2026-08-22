<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'user_id',
    'name',
    'slug',
])]

class Tag extends Model
{
    use SoftDeletes;

    /**
     * Utilisateur qui a créé le tag.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class);
    }
}