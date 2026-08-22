<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'author_id',
    'type_publication_id',
    'cover_media_file_id',
    'wp_publication_id',
    'title',
    'slug',
    'title_truncate',
    'content',
    'truncate_content',
    'truncate_content_max',
    'status',
    'comment_status',
    'date_publish',
    'date_modified',
    'source',
    'wp_link',
])]

class Publication extends Model
{
    use SoftDeletes;

    /**
     * Utilisateur qui a créé la publication.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Auteur de la publication.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Type de la publication.
     */
    public function typePublication(): BelongsTo
    {
        return $this->belongsTo(TypePublication::class);
    }

    /**
     * Les catégories de la publication.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Les tags de la publication.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }


    public function likes(): HasMany
    {
        return $this->hasMany(PublicationLike::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(PublicationView::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Média utilisé comme couverture.
     */
    public function coverMediaFile(): BelongsTo
    {
        return $this->belongsTo(
            MediaFile::class,
            'cover_media_file_id'
        );
    }

    /**
     * Les fichiers de la publication.
     */
    public function mediaFiles(): BelongsToMany
    {
        return $this->belongsToMany(
            MediaFile::class,
            'media_file_publication'
        );
    }
}