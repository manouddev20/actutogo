<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'user_id',
        'prenom',
        'nom_complet',
        'nom_auteur',
        'slug',
        'telephone',
        'adresse',
        'description',
        'status',
        'wp_author_id',
    ];

    protected $casts = [
        'status' => 'boolean',
        'wp_author_id' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }
}