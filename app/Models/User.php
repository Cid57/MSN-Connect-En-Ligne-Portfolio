<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar',
        'is_admin',
        'is_active',
        'status_id',
        'status_message',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    /**
     * Relation : Le statut de l'utilisateur
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Relation : Les channels (conversations/groupes) de l'utilisateur
     */
    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class)
                    ->withPivot(['is_admin', 'is_muted', 'joined_at', 'last_read_at'])
                    ->withTimestamps();
    }

    /**
     * Relation : Les messages envoyés par l'utilisateur
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Relation : Les channels créés par l'utilisateur
     */
    public function createdChannels(): HasMany
    {
        return $this->hasMany(Channel::class, 'created_by');
    }

    /**
     * Scope : Utilisateurs actifs uniquement
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope : Administrateurs uniquement
     */
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    /**
     * Accessor : Nom complet de l'utilisateur
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}") ?: $this->name;
    }

    /**
     * Vérifie si l'utilisateur est en ligne (vu dans les 5 dernières minutes)
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at?->greaterThan(now()->subMinutes(5)) ?? false;
    }
}
