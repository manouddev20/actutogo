<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    /**
     * Champs autorisés pour l'insertion en masse.
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Une permission peut appartenir à plusieurs rôles.
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_permission',
            'permission_id',
            'role_id'
        );
    }
}