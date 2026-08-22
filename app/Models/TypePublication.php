<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;


#[Fillable([
    'user_id',
    'name',
    'slug',
])]

class TypePublication extends Model
{
    use SoftDeletes;

    /**
     * Utilisateur qui a créé le type de publication.
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