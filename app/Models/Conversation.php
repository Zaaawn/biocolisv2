<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'annonce_id', 'acheteur_id', 'vendeur_id', 'commande_id',
        'dernier_message_at', 'nb_messages',
        'non_lus_acheteur', 'non_lus_vendeur',
        'archive_acheteur', 'archive_vendeur',
    ];

    protected function casts(): array
    {
        return [
            'dernier_message_at' => 'datetime',
            'archive_acheteur'   => 'boolean',
            'archive_vendeur'    => 'boolean',
        ];
    }

    // ── RELATIONS ──────────────────────────────────────────────────────────────

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function acheteur()
    {
        return $this->belongsTo(User::class, 'acheteur_id');
    }

    public function vendeur()
    {
        return $this->belongsTo(User::class, 'vendeur_id');
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function dernierMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    // ── SCOPES ─────────────────────────────────────────────────────────────────

    /**
     * Toutes les conversations où l'utilisateur est participant
     */
    public function scopePourUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('acheteur_id', $userId)
              ->orWhere('vendeur_id', $userId);
        });
    }

    /**
     * ✅ CORRIGÉ : le orWhere global doit être wrappé pour éviter
     * que les conditions s'appliquent à toute la requête
     *
     * AVANT (bugué) :
     *   WHERE acheteur_id=X AND archive_acheteur=0
     *   OR vendeur_id=X AND archive_vendeur=0
     *   → retourne aussi des convs archivées si l'autre condition match
     *
     * APRÈS (correct) :
     *   WHERE (acheteur_id=X AND archive_acheteur=0)
     *      OR (vendeur_id=X AND archive_vendeur=0)
     */
    public function scopeNonArchivees($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where(function ($sub) use ($userId) {
                $sub->where('acheteur_id', $userId)
                    ->where('archive_acheteur', false);
            })->orWhere(function ($sub) use ($userId) {
                $sub->where('vendeur_id', $userId)
                    ->where('archive_vendeur', false);
            });
        });
    }

    // ── HELPERS ────────────────────────────────────────────────────────────────

    /**
     * Retourne l'autre participant (pas l'utilisateur connecté)
     */
    public function getAutreParticipantAttribute(): ?User
    {
        $userId = Auth::id();

        // Charger les relations si pas encore chargées
        if (!$this->relationLoaded('acheteur')) $this->load('acheteur');
        if (!$this->relationLoaded('vendeur'))  $this->load('vendeur');

        if ($this->acheteur_id === $userId) return $this->vendeur;
        if ($this->vendeur_id  === $userId) return $this->acheteur;

        return null;
    }

    /**
     * Nb de messages non lus pour l'utilisateur connecté
     */
    public function getNonLusPourMoiAttribute(): int
    {
        $userId = Auth::id();
        if ($this->acheteur_id === $userId) return (int) $this->non_lus_acheteur;
        if ($this->vendeur_id  === $userId) return (int) $this->non_lus_vendeur;
        return 0;
    }

    /**
     * Marque tous les messages comme lus pour l'utilisateur
     */
    public function marquerCommeLu(int $userId): void
    {
        $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'lu_at' => now()]);

        if ($this->acheteur_id === $userId) {
            $this->update(['non_lus_acheteur' => 0]);
        } elseif ($this->vendeur_id === $userId) {
            $this->update(['non_lus_vendeur' => 0]);
        }
    }

    /**
     * Incrémente le compteur non-lus pour le destinataire
     */
    public function incrementerNonLus(int $senderId): void
    {
        if ($this->acheteur_id === $senderId) {
            $this->increment('non_lus_vendeur');
        } else {
            $this->increment('non_lus_acheteur');
        }
    }
}
