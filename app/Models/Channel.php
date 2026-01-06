<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_group',
        'is_active',
        'created_by',
        'last_message_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_group' => 'boolean',
            'is_active' => 'boolean',
            'last_message_at' => 'datetime',
        ];
    }

    /**
     * Relation : Le créateur du channel
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation : Les membres du channel
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withPivot(['is_admin', 'is_muted', 'joined_at', 'last_read_at'])
                    ->withTimestamps();
    }

    /**
     * Relation : Les messages du channel
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    /**
     * Relation : Le dernier message du channel
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Scope : Conversations privées uniquement
     */
    public function scopePrivate($query)
    {
        return $query->where('is_group', false);
    }

    /**
     * Scope : Groupes uniquement
     */
    public function scopeGroups($query)
    {
        return $query->where('is_group', true);
    }

    /**
     * Scope : Channels actifs uniquement
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope : Tri par activité récente
     */
    public function scopeRecentActivity($query)
    {
        return $query->orderByDesc('last_message_at');
    }

    /**
     * Vérifie si un utilisateur est membre du channel
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Vérifie si un utilisateur est admin du channel
     */
    public function isAdmin(User $user): bool
    {
        return $this->members()
                    ->where('user_id', $user->id)
                    ->wherePivot('is_admin', true)
                    ->exists();
    }

    /**
     * Met à jour la date du dernier message
     */
    public function updateLastMessageTime(): void
    {
        $this->update(['last_message_at' => now()]);
    }
}
