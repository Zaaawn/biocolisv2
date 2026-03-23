<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id', 'sender_id', 'contenu', 'images',
        'type', 'is_read', 'lu_at',
        'systeme_type', 'systeme_data',
    ];

    protected function casts(): array
    {
        return [
            'images'       => 'array',
            'systeme_data' => 'array',
            'is_read'      => 'boolean',
            'lu_at'        => 'datetime',
        ];
    }

    // ── RELATIONS ──────────────────────────────────────────────────────────────

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // ── BOOT (met à jour la conversation après création) ───────────────────────

    protected static function boot()
    {
        parent::boot();

        static::created(function (Message $message) {
            $message->conversation->update([
                'dernier_message_at' => $message->created_at,
                'nb_messages'        => $message->conversation->nb_messages + 1,
            ]);

            $message->conversation->incrementerNonLus($message->sender_id);
        });
    }

    // ── HELPERS ────────────────────────────────────────────────────────────────

    public function estDeMoi(): bool
    {
        return $this->sender_id === Auth::id();
    }

    public function getPremiereImageAttribute(): ?string
    {
        if (!empty($this->images[0])) {
            return asset('storage/' . $this->images[0]);
        }
        return null;
    }

    // Crée un message système (ex: "Commande passée", "Livraison confirmée")
    public static function systeme(int $conversationId, string $type, array $data = []): static
    {
        return static::create([
            'conversation_id' => $conversationId,
            'sender_id'       => 1, // user système (admin ID 1)
            'type'            => 'systeme',
            'systeme_type'    => $type,
            'systeme_data'    => $data,
            'contenu'         => null,
        ]);
    }
}
