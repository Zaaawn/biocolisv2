<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnonceOption extends Model
{
    use HasFactory;

    protected $table = 'annonce_options';

    protected $fillable = [
        'annonce_id', 'user_id', 'type', 'prix_paye',
        'stripe_payment_intent_id', 'debut_at', 'fin_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'debut_at'   => 'datetime',
            'fin_at'     => 'datetime',
            'is_active'  => 'boolean',
            'prix_paye'  => 'float',
        ];
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActives($query)
    {
        return $query->where('is_active', true)
                     ->where(fn($q) => $q->whereNull('fin_at')->orWhere('fin_at', '>', now()));
    }

    // Tarifs standards
    public static function tarifParType(string $type): float
    {
        return match ($type) {
            'mise_en_avant' => 1.99,
            'epinglage'     => 2.99,
            'remontee'      => 2.49,
            'prolongation'  => 0.99,
            'galerie'       => 0.99,
            'urgent'        => 1.49,
            default         => 0.00,
        };
    }

    // Durée en jours par type
    public static function dureeParType(string $type): ?int
    {
        return match ($type) {
            'mise_en_avant' => 7,
            'epinglage'     => 30,
            'remontee'      => 15,
            'prolongation'  => 30,
            'urgent'        => 7,
            default         => null,
        };
    }

    // Après création, active l'option sur l'annonce
    protected static function boot()
    {
        parent::boot();

        static::created(function (AnnonceOption $option) {
            match ($option->type) {
                'mise_en_avant' => $option->annonce->update(['est_mise_en_avant' => true]),
                'epinglage'     => $option->annonce->update(['est_epinglee' => true]),
                'remontee'      => $option->annonce->update(['remontee_at' => now()]),
                default         => null,
            };
        });
    }
}
