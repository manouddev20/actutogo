<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeMediaFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'mime_type',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function mediaFiles(): HasMany
    {
        return $this->hasMany(MediaFile::class, 'type_media_file_id');
    }
}