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
        'media_file_type_id',
        'user_id',
        'wp_file_id',
        'name',
        'original_name',
        'slug',
        'file_url',
        'path',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'duration',
        'title',
        'caption',
        'alt_text',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'wp_file_id' => 'integer',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration' => 'decimal:3',
    ];

    public function mediaFileType(): BelongsTo
    {
        return $this->belongsTo(
            TypeMediaFile::class,
            'type_media_file_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(
            Publication::class,
            'publication_media_file',
            'media_file_id',
            'publication_id'
        )->withPivot('role', 'position')->withTimestamps();
    }
        
}