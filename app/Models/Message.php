<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'channel_id',
        'user_id',
        'content',
        'attachment',
        'attachment_type',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function booted(): void
    {
        // Met à jour automatiquement last_message_at du channel
        static::created(function (Message $message) {
            $message->channel->updateLastMessageTime();
        });
    }

    /**
     * Relation : Le channel du message
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Relation : L'auteur du message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope : Messages non lus uniquement
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope : Messages avec pièce jointe uniquement
     */
    public function scopeWithAttachment($query)
    {
        return $query->whereNotNull('attachment');
    }

    /**
     * Scope : Messages d'un channel spécifique
     */
    public function scopeForChannel($query, int $channelId)
    {
        return $query->where('channel_id', $channelId);
    }

    /**
     * Marque le message comme lu
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Vérifie si le message a une pièce jointe
     */
    public function hasAttachment(): bool
    {
        return !is_null($this->attachment);
    }

    /**
     * Obtient l'URL de la pièce jointe
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        return asset('storage/' . $this->attachment);
    }
}
