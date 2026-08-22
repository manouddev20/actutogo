<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'email',
    'username',
    'password',
    'role_id',
])]

#[Hidden([
    'password',
    'remember_token',
])]

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Relation : un utilisateur appartient à un rôle.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Retourne l'identifiant utilisé dans le JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Un utilisateur possède un abonnement newsletter.
     */
    public function newsletter(): HasOne
    {
        return $this->hasOne(NewsLetter::class);
    }

    /**
     * Les messages de contact envoyés par l'utilisateur.
     */
    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    /**
     * Les OTP de l'utilisateur.
     */
    public function otps(): HasMany
    {
        return $this->hasMany(Otp::class);
    }

    /**
     * Types de publications créés par l'utilisateur.
     */
    public function typePublications(): HasMany
    {
        return $this->hasMany(TypePublication::class);
    }

    /**
     * Catégories créées par l'utilisateur.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Tags créés par l'utilisateur.
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * Auteurs ajoutés par l'utilisateur.
     */
    public function authors(): HasMany
    {
        return $this->hasMany(Author::class);
    }

    /**
     * Types de fichiers créés par l'utilisateur.
     */
    public function fileTypes(): HasMany
    {
        return $this->hasMany(FileType::class);
    }

    /**
     * Fichiers ajoutés par l'utilisateur.
     */
    public function mediaFiles(): HasMany
    {
        return $this->hasMany(MediaFile::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }

    public function publicationLikes(): HasMany
    {
        return $this->hasMany(PublicationLike::class);
    }

    public function publicationViews(): HasMany
    {
        return $this->hasMany(PublicationView::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function commentLikes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    public function commentReports(): HasMany
    {
        return $this->hasMany(CommentReport::class);
    }
    /**
     * Retourne les claims personnalisés du JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [
            // 'role' => $this->role?->slug,
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}