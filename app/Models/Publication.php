<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type_publication_id',
        'author_id',
        'title',
        'slug',
        'title_truncated',
        'content',
        'summary',
        'excerpt',
        'views_count',
        'likes_count',
        'shares_count',
        'comments_count',
        'source',
        'status',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'shares_count' => 'integer',
        'comments_count' => 'integer',
        'status' => 'boolean',
        'published_at' => 'datetime', 
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typePublication(): BelongsTo
    {
        return $this->belongsTo(TypePublication::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'category_publication',
            'publication_id',
            'category_id'
        )->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'publication_tag',
            'publication_id',
            'tag_id'
        )->withTimestamps();
    }

    public function coverMediaFile(): BelongsTo
    {
        return $this->belongsTo(
            MediaFile::class,
            'cover_media_file_id'
        );
    }

    public function mediaFiles(): BelongsToMany
    {
        return $this->belongsToMany(
            MediaFile::class,
            'publication_media_file',
            'publication_id',
            'media_file_id'
        )->withPivot('role', 'position')->withTimestamps();
    }

    public function views(): HasMany
    {
        return $this->hasMany(PublicationView::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PublicationLike::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(PublicationShare::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}