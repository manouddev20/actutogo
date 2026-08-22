<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
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
     * Un rôle possède plusieurs utilisateurs.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Un rôle possède plusieurs permissions.
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permission',
            'role_id',
            'permission_id'
        );
    }
}