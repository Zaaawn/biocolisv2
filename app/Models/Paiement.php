<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_id', 'user_id', 'type', 'statut', 'montant', 'devise',
        'stripe_id', 'stripe_type', 'stripe_metadata',
        'facture_numero', 'facture_url', 'description', 'traite_at',
    ];

    protected function casts(): array
    {
        return [
            'montant'          => 'float',
            'stripe_metadata'  => 'array',
            'traite_at'        => 'datetime',
        ];
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estReussi(): bool
    {
        return $this->statut === 'succes';
    }
}

// ─────────────────────────────────────────────────────────────────────────────
