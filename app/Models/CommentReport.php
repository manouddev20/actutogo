<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'comment_id',
        'user_id',
        'visitor_identifier',
        'reason',
        'description',
        'ip_address',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}