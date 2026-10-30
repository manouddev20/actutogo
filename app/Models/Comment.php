<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'publication_id',
        'user_id',
        'parent_id',
        'nom_complet',
        'email',
        'content',
        'likes_count',
        'reports_count',
        'status',
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'reports_count' => 'integer',
        'status' => 'boolean',
    ];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(CommentReport::class);
    }
}