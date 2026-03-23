<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Annonce extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'titre', 'slug', 'description', 'prix', 'unite_prix',
        'type_produit', 'categorie', 'label', 'date_recolte',
        'quantite_disponible', 'quantite_min_commande', 'poids_unitaire',
        'localisation', 'ville', 'code_postal', 'latitude', 'longitude',
        'rayon_livraison_km', 'livraison_main_propre', 'livraison_point_relais',
        'livraison_domicile', 'livraison_locker', 'photos',
        'disponible_a_partir_de', 'disponible_jusqu_a', 'statut',
        'est_mise_en_avant', 'est_epinglee', 'remontee_at',
        'nb_vues', 'nb_likes', 'nb_commandes', 'note_moyenne',
    ];

    protected function casts(): array
    {
        return [
            'photos'                  => 'array',
            'date_recolte'            => 'date',
            'disponible_a_partir_de'  => 'date',
            'disponible_jusqu_a'      => 'date',
            'remontee_at'             => 'datetime',
            'livraison_main_propre'   => 'boolean',
            'livraison_point_relais'  => 'boolean',
            'livraison_domicile'      => 'boolean',
            'livraison_locker'        => 'boolean',
            'est_mise_en_avant'       => 'boolean',
            'est_epinglee'            => 'boolean',
            'prix'                    => 'float',
            'quantite_disponible'     => 'float',
            'latitude'                => 'float',
            'longitude'               => 'float',
        ];
    }

    // ── BOOT (auto-slug) ───────────────────────────────────────────────────────

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Annonce $annonce) {
            if (empty($annonce->slug)) {
                $annonce->slug = static::generateUniqueSlug($annonce->titre);
            }
        });

        static::updating(function (Annonce $annonce) {
            if ($annonce->isDirty('titre')) {
                $annonce->slug = static::generateUniqueSlug($annonce->titre, $annonce->id);
            }
        });
    }

    public static function generateUniqueSlug(string $titre, ?int $excludeId = null): string
    {
        $base = Str::slug($titre);
        $slug = $base;
        $i    = 1;

        while (
            static::where('slug', $slug)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    // ── RELATIONS ──────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function options()
    {
        return $this->hasMany(AnnonceOption::class);
    }

    public function optionsActives()
    {
        return $this->hasMany(AnnonceOption::class)
            ->where('is_active', true)
            ->where('fin_at', '>', now());
    }

    public function commandeLignes()
    {
        return $this->hasMany(CommandeLigne::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function likedBy()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function panierLignes()
    {
        return $this->hasMany(PanierLigne::class);
    }

    // ── SCOPES ─────────────────────────────────────────────────────────────────

    public function scopeDisponibles($query)
    {
        return $query->where('statut', 'disponible');
    }

    public function scopeMisesEnAvant($query)
    {
        return $query->where('est_mise_en_avant', true);
    }

    public function scopeEpinglees($query)
    {
        return $query->where('est_epinglee', true);
    }

    // Tri intelligent : épinglées > mises en avant > remontées > date
    public function scopeTriIntelligent($query)
    {
        return $query->orderByRaw("
            CASE
                WHEN est_epinglee = 1 THEN 1
                WHEN est_mise_en_avant = 1 THEN 2
                WHEN remontee_at IS NOT NULL AND remontee_at > DATE_SUB(NOW(), INTERVAL 15 DAY) THEN 3
                ELSE 4
            END
        ")->orderBy('created_at', 'desc');
    }

    public function scopeProcheDe($query, float $lat, float $lng, int $rayonKm = 50)
    {
        return $query->selectRaw("
            *,
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude))
                * cos(radians(longitude) - radians(?))
                + sin(radians(?)) * sin(radians(latitude))
            )) AS distance
        ", [$lat, $lng, $lat])
        ->having('distance', '<=', $rayonKm)
        ->orderBy('distance');
    }

    public function scopeExpirees($query)
    {
        return $query->where('disponible_jusqu_a', '<', now())
                     ->where('statut', '!=', 'expiree');
    }

    // ── HELPERS ────────────────────────────────────────────────────────────────
public function getPremierePhotoAttribute(): ?string
{
    if (!empty($this->photos[0])) {
        return asset('storage/' . $this->photos[0]);
    }
    return asset('images/placeholder-produit.svg');
}

    public function getModesLivraisonAttribute(): array
    {
        $modes = [];
        if ($this->livraison_main_propre)  $modes[] = 'main_propre';
        if ($this->livraison_point_relais) $modes[] = 'point_relais';
        if ($this->livraison_domicile)     $modes[] = 'domicile';
        if ($this->livraison_locker)       $modes[] = 'locker';
        return $modes;
    }

    public function isDisponible(): bool
    {
        return $this->statut === 'disponible'
            && ($this->disponible_jusqu_a === null || $this->disponible_jusqu_a->isFuture());
    }

    public function incrementVues(): void
    {
        $this->increment('nb_vues');
    }

    public function recalculerNote(): void
    {
        $stats = $this->ratings()
            ->selectRaw('AVG(note) as moyenne, COUNT(*) as total')
            ->first();

        $this->update([
            'note_moyenne' => round($stats->moyenne ?? 0, 2),
        ]);
    }
}
