<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'slug', 'cible', 'prix_mensuel', 'prix_annuel',
        'stripe_price_id_mensuel', 'stripe_price_id_annuel',
        'fonctionnalites', 'nb_annonces_max', 'commission_pct',
        'livraison_incluse', 'is_active', 'ordre',
    ];

    protected function casts(): array
    {
        return [
            'fonctionnalites'   => 'array',
            'prix_mensuel'      => 'float',
            'prix_annuel'       => 'float',
            'livraison_incluse' => 'boolean',
            'is_active'         => 'boolean',
        ];
    }

    public function abonnements()
    {
        return $this->hasMany(Abonnement::class);
    }

    public function scopeActifs($query)
    {
        return $query->where('is_active', true)->orderBy('ordre');
    }

    public function scopePourB2B($query)
    {
        return $query->where('cible', 'b2b');
    }

    public function scopePourProducteur($query)
    {
        return $query->where('cible', 'producteur');
    }
}