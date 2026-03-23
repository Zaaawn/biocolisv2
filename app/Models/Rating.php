<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ─────────────────────────────────────────────────────────────────────────────

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_id', 'annonce_id', 'auteur_id', 'cible_id',
        'sens', 'note', 'commentaire', 'criteres',
        'is_visible', 'is_signale', 'reponse_vendeur', 'repondu_at',
    ];

    protected function casts(): array
    {
        return [
            'criteres'    => 'array',
            'is_visible'  => 'boolean',
            'is_signale'  => 'boolean',
            'repondu_at'  => 'datetime',
            'note'        => 'integer',
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

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }

    public function cible()
    {
        return $this->belongsTo(User::class, 'cible_id');
    }

    public function scopeVisibles($query)
    {
        return $query->where('is_visible', true);
    }

    // Après création, recalcule les notes du vendeur et de l'annonce
    protected static function boot()
    {
        parent::boot();

        static::created(function (Rating $rating) {
            $rating->cible->recalculerNote();
            $rating->annonce->recalculerNote();
        });

        static::updated(function (Rating $rating) {
            $rating->cible->recalculerNote();
            $rating->annonce->recalculerNote();
        });
    }
}

// ─────────────────────────────────────────────────────────────────────────────



// ─────────────────────────────────────────────────────────────────────────────



// ─────────────────────────────────────────────────────────────────────────────


