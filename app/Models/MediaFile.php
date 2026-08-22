<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MediaFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'file_type_id',
        'wp_file_id',
        'file_name',
        'file_slug',
        'file_url',
        'wp_file',
        'caption',
    ];

    /**
     * L'utilisateur ayant ajouté/importé le fichier.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Type du fichier.
     */
    public function fileType(): BelongsTo
    {
        return $this->belongsTo(FileType::class);
    }

    /**
     * Publications utilisant ce fichier.
     */
    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(
            Publication::class,
            'publication_media_file',
            'media_file_id',
            'publication_id'
        );
    }

    /**
     * Publications utilisant ce fichier comme couverture.
     */
    public function coverPublications()
    {
        return $this->hasMany(
            Publication::class,
            'cover_media_file_id'
        );
    }
}