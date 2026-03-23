<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Commande extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero', 'acheteur_id', 'vendeur_id', 'statut',
        'sous_total', 'frais_livraison', 'frais_service', 'total_ttc', 'montant_vendeur',
        'stripe_payment_intent_id', 'stripe_charge_id', 'stripe_transfer_id',
        'paye_at', 'rembourse_at', 'montant_rembourse',
        'adresse_livraison', 'ville_livraison', 'code_postal_livraison',
        'latitude_livraison', 'longitude_livraison',
        'note_acheteur', 'note_interne',
        'annule_at', 'annule_par', 'motif_annulation',
        'livree_at', 'terminee_at',
    ];

    protected function casts(): array
    {
        return [
            'paye_at'       => 'datetime',
            'rembourse_at'  => 'datetime',
            'annule_at'     => 'datetime',
            'livree_at'     => 'datetime',
            'terminee_at'   => 'datetime',
            'sous_total'    => 'float',
            'frais_livraison' => 'float',
            'frais_service' => 'float',
            'total_ttc'     => 'float',
            'montant_vendeur' => 'float',
            'montant_rembourse' => 'float',
        ];
    }

    // ── BOOT (auto-numéro) ─────────────────────────────────────────────────────

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Commande $commande) {
            if (empty($commande->numero)) {
                $commande->numero = static::genererNumero();
            }
        });
    }

    public static function genererNumero(): string
    {
        do {
            $numero = 'BIO-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (static::where('numero', $numero)->exists());

        return $numero;
    }

    // ── RELATIONS ──────────────────────────────────────────────────────────────

    public function acheteur()
    {
        return $this->belongsTo(User::class, 'acheteur_id');
    }

    public function vendeur()
    {
        return $this->belongsTo(User::class, 'vendeur_id');
    }

    public function lignes()
    {
        return $this->hasMany(CommandeLigne::class);
    }

    public function livraison()
    {
        return $this->hasOne(Livraison::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }

    // ── SCOPES ─────────────────────────────────────────────────────────────────

    public function scopePayees($query)
    {
        return $query->whereNotNull('paye_at');
    }

    public function scopeEnCours($query)
    {
        return $query->whereIn('statut', ['payee', 'en_preparation', 'prete', 'en_livraison']);
    }

    // ── HELPERS ────────────────────────────────────────────────────────────────

    public function estPayee(): bool
    {
        return $this->paye_at !== null;
    }

    public function estAnnulable(): bool
    {
        return in_array($this->statut, ['en_attente', 'paiement_en_cours', 'payee']);
    }

    public function estNotable(): bool
    {
        return in_array($this->statut, ['livree', 'terminee']);
    }

    // Calcule la part vendeur après commission Biocolis
    public function calculerMontantVendeur(float $commissionPct = 12): float
    {
        return round($this->sous_total * (1 - $commissionPct / 100), 2);
    }

    public function changerStatut(string $nouveauStatut): void
    {
        $this->update(['statut' => $nouveauStatut]);

        // Met à jour les timestamps liés au statut
        match ($nouveauStatut) {
            'livree'   => $this->update(['livree_at' => now()]),
            'terminee' => $this->update(['terminee_at' => now()]),
            'annulee'  => $this->update(['annule_at' => now()]),
            default    => null,
        };
    }
}

// ─────────────────────────────────────────────────────────────────────────────

class CommandeLigne extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_id', 'annonce_id', 'titre_annonce', 'prix_unitaire',
        'unite_prix', 'quantite', 'sous_total', 'photo_annonce',
    ];

    protected function casts(): array
    {
        return [
            'prix_unitaire' => 'float',
            'quantite'      => 'float',
            'sous_total'    => 'float',
        ];
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }
}

// ─────────────────────────────────────────────────────────────────────────────

class Livraison extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_id', 'mode', 'statut', 'tarif',
        'creneau_debut', 'creneau_fin', 'adresse_rdv', 'latitude_rdv', 'longitude_rdv',
        'point_relais_nom', 'point_relais_adresse', 'point_relais_code',
        'locker_id', 'locker_code_acces', 'locker_disponible_jusqu_at',
        'numero_suivi', 'livree_at', 'signature_url', 'note',
    ];

    protected function casts(): array
    {
        return [
            'creneau_debut'               => 'datetime',
            'creneau_fin'                 => 'datetime',
            'livree_at'                   => 'datetime',
            'locker_disponible_jusqu_at'  => 'datetime',
            'tarif'                       => 'float',
            'latitude_rdv'                => 'float',
            'longitude_rdv'               => 'float',
        ];
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    // Tarifs standards par mode
    public static function tarifParMode(string $mode): float
    {
        return match ($mode) {
            'main_propre'   => 0.00,
            'point_relais'  => 3.00,
            'domicile'      => 6.00,
            'locker'        => 2.50,
            default         => 0.00,
        };
    }

    public function getLabelModeAttribute(): string
    {
        return match ($this->mode) {
            'main_propre'   => 'Remise en main propre',
            'point_relais'  => 'Point relais',
            'domicile'      => 'Livraison à domicile',
            'locker'        => 'Casier connecté',
            default         => $this->mode,
        };
    }
}
