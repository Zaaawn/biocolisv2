<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'prenom', 'nom', 'username', 'email', 'password',
        'role', 'adresse', 'latitude', 'longitude', 'telephone',
        'photo_profil', 'societe_nom', 'siret', 'societe_adresse',
        'tva_intracommunautaire', 'stripe_account_id', 'stripe_account_status',
        'abonnement_actif_id', 'is_active', 'is_banned',
        'banned_at', 'ban_reason', 'nb_annonces', 'nb_ventes',
        'nb_achats', 'note_moyenne', 'nb_avis', 
        'iban', 'bic', 'titulaire_compte',
        'solde_disponible', 'solde_en_attente', 'total_recu', 'dernier_virement_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_banned'         => 'boolean',
            'banned_at'         => 'datetime',
            'note_moyenne'      => 'float',
            'latitude'          => 'float',
            'longitude'         => 'float',
        ];
    }

    // ── RELATIONS ──────────────────────────────────────────────────────────────

    public function annonces()
    {
        return $this->hasMany(Annonce::class);
    }

   public function abonnements()
{
    return $this->hasMany(Abonnement::class);
}

public function abonnementActif()
{
    return $this->hasOne(Abonnement::class)->where('statut', 'actif')->latest();
}

    // Commandes en tant qu'acheteur
    public function achats()
    {
        return $this->hasMany(Commande::class, 'acheteur_id');
    }

    // Commandes en tant que vendeur
    public function ventes()
    {
        return $this->hasMany(Commande::class, 'vendeur_id');
    }

    // Conversations en tant qu'acheteur
    public function conversationsAcheteur()
    {
        return $this->hasMany(Conversation::class, 'acheteur_id');
    }

    // Conversations en tant que vendeur
    public function conversationsVendeur()
    {
        return $this->hasMany(Conversation::class, 'vendeur_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function likes()
    {
        return $this->belongsToMany(Annonce::class, 'likes')->withTimestamps();
    }

    // Avis reçus (en tant que vendeur ou acheteur)
    public function avisRecus()
    {
        return $this->hasMany(Rating::class, 'cible_id');
    }

    // Avis donnés
    public function avisDonnes()
    {
        return $this->hasMany(Rating::class, 'auteur_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function panierLignes()
    {
        return $this->hasMany(PanierLigne::class);
    }

    public function signalements()
    {
        return $this->hasMany(Signalement::class, 'auteur_id');
    }

    // ── HELPERS ────────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isProfessionnel(): bool
    {
        return in_array($this->role, ['professionnel', 'b2b']);
    }

    public function isB2B(): bool
    {
        return $this->role === 'b2b';
    }

    public function hasStripeAccount(): bool
    {
        return $this->stripe_account_status === 'actif';
    }

    public function hasAbonnementActif(): bool
    {
        return $this->abonnement_actif_id !== null;
    }

    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

        public function getPhotoProfilUrlAttribute(): string
    {
        // Si l'utilisateur a uploadé une photo → on l'affiche
        if ($this->photo_profil && $this->photo_profil !== 'photos_profil/default.jpg') {
            return asset('storage/' . $this->photo_profil);
        }
 
        // Sinon → avatar généré automatiquement avec les initiales (service gratuit)
        $initiales = urlencode(substr($this->prenom ?? 'U', 0, 1) . substr($this->nom ?? '', 0, 1));
        return "https://ui-avatars.com/api/?name={$initiales}&background=16a34a&color=fff&size=128&bold=true&rounded=true";
    }

    // Recalcule et met à jour la note moyenne du user
    public function recalculerNote(): void
    {
        $stats = $this->avisRecus()
            ->where('is_visible', true)
            ->selectRaw('AVG(note) as moyenne, COUNT(*) as total')
            ->first();

        $this->update([
            'note_moyenne' => round($stats->moyenne ?? 0, 2),
            'nb_avis'      => $stats->total ?? 0,
        ]);
    }
    // Toutes les conversations (acheteur + vendeur)
    public function conversations()
    {
        return Conversation::where('acheteur_id', $this->id)
            ->orWhere('vendeur_id', $this->id);
    }

    public function sendPasswordResetNotification($token): void
{
    $this->notify(new \App\Notifications\ResetPasswordBiocolis($token));
}

}
