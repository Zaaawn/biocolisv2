<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanierLigne extends Model
{
    use HasFactory;

    protected $table = 'panier_lignes';

    protected $fillable = [
        'user_id', 'annonce_id', 'quantite', 'mode_livraison',
    ];

    protected function casts(): array
    {
        return [
            'quantite' => 'float',
        ];
    }

    // ── RELATIONS ──────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    // ── ACCESSEURS ─────────────────────────────────────────────────────────────

    /**
     * Sous-total = quantité × prix unitaire
     * Protégé contre annonce null ou prix null
     */
    public function getSousTotalAttribute(): float
    {
        if (!$this->annonce) return 0.0;
        return round($this->quantite * ($this->annonce->prix ?? 0), 2);
    }

    /**
     * Frais de livraison selon le mode choisi
     * Tarifs : main_propre=0, point_relais=3, domicile=6, locker=2.5
     */
    public function getFraisLivraisonAttribute(): float
    {
        return match ($this->mode_livraison) {
            'point_relais' => 3.00,
            'domicile'     => 6.00,
            'locker'       => 2.50,
            default        => 0.00, // main_propre ou null
        };
    }

    /**
     * Total ligne = sous-total + frais livraison
     */
    public function getTotalLigneAttribute(): float
    {
        return round($this->sous_total + $this->frais_livraison, 2);
    }
}
