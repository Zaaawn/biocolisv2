<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'plan_id', 'statut', 'periodicite',
        'stripe_subscription_id', 'stripe_customer_id',
        'debut_at', 'fin_at', 'annule_at', 'prochain_paiement_at', 'montant',
    ];

    protected function casts(): array
    {
        return [
            'debut_at'             => 'datetime',
            'fin_at'               => 'datetime',
            'annule_at'            => 'datetime',
            'prochain_paiement_at' => 'datetime',
            'montant'              => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    public function estActif(): bool
    {
        return $this->statut === 'actif'
            && ($this->fin_at === null || $this->fin_at->isFuture());
    }
}