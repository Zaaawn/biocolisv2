<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signalement extends Model
{
    use HasFactory;

    protected $fillable = [
        'auteur_id', 'cible_type', 'cible_id',
        'motif', 'description', 'statut',
        'note_admin', 'traite_par', 'traite_at',
    ];

    protected function casts(): array
    {
        return [
            'traite_at' => 'datetime',
        ];
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }

    public function cible()
    {
        return $this->morphTo();
    }

    public function traitePar()
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }
}
