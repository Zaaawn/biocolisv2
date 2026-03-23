<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\AnnonceOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AnnonceController extends Controller
{
    // ── INDEX — Liste avec filtres + recherche + géoloc ────────────────────────
    public function index(Request $request)
    {
        $query = Annonce::with(['user', 'optionsActives'])
            ->disponibles();

        // Recherche texte
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('categorie', 'like', "%$search%")
                  ->orWhere('ville', 'like', "%$search%");
            });
        }

        // Filtre type produit
        if ($request->filled('type')) {
            $query->where('type_produit', $request->type);
        }

        // Filtre label (bio, local...)
        if ($request->filled('label')) {
            $query->where('label', $request->label);
        }

        // Filtre prix
        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }
        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }

        // Filtre mode de livraison
        if ($request->filled('livraison')) {
            match ($request->livraison) {
                'main_propre'   => $query->where('livraison_main_propre', true),
                'point_relais'  => $query->where('livraison_point_relais', true),
                'domicile'      => $query->where('livraison_domicile', true),
                'locker'        => $query->where('livraison_locker', true),
                default         => null,
            };
        }

        // Filtre géolocalisation (proche de moi)
        if ($request->filled('lat') && $request->filled('lng')) {
            $rayon = $request->input('rayon', 30);
            $query->procheDe($request->lat, $request->lng, $rayon);
        } else {
            // Tri intelligent par défaut (épinglées > mises en avant > récentes)
            $query->triIntelligent();
        }

        $annonces = $query->paginate(12)->withQueryString();

        // Stats pour les filtres
        $prixMin = Annonce::disponibles()->min('prix');
        $prixMax = Annonce::disponibles()->max('prix');

        return view('annonces.index', compact('annonces', 'prixMin', 'prixMax'));
    }

    // ── SHOW — Détail d'une annonce ────────────────────────────────────────────
    public function show(Annonce $annonce)
    {
        // Annonce supprimée ou indisponible = 404
        if (!$annonce->isDisponible() && Auth::id() !== $annonce->user_id) {
            abort(404);
        }

        $annonce->load(['user', 'ratings.auteur', 'optionsActives']);
        $annonce->incrementVues();

        // Annonces similaires
        $similaires = Annonce::disponibles()
            ->where('type_produit', $annonce->type_produit)
            ->where('id', '!=', $annonce->id)
            ->limit(4)
            ->get();

        // L'utilisateur a-t-il liké ?
        $estLike = Auth::check()
            ? $annonce->likedBy()->where('user_id', Auth::id())->exists()
            : false;

        // L'utilisateur a-t-il déjà ce produit dans son panier ?
        $dansPanier = Auth::check()
            ? $annonce->panierLignes()->where('user_id', Auth::id())->exists()
            : false;

        return view('annonces.show', compact('annonce', 'similaires', 'estLike', 'dansPanier'));
    }

    // ── CREATE ─────────────────────────────────────────────────────────────────
    public function create()
    {
        // ✅ Onboarding Stripe DIFFÉRÉ — le vendeur peut déposer une annonce
        // librement. Un rappel s'affiche dans le dashboard si pas encore activé.
        return view('annonces.create');
    }

    // ── STORE ──────────────────────────────────────────────────────────────────
   public function store(Request $request)
{
    // ── Vérification quota annonces selon le plan ──────────────────────────
    $plan        = Auth::user()->abonnementActif?->plan;
    $nbMax       = $plan?->nb_annonces_max ?? 2;
    $nbActuelles = Annonce::where('user_id', Auth::id())
                          ->where('statut', 'disponible')
                          ->count();

    if ($nbMax > 0 && $nbActuelles >= $nbMax) {
        return back()->with('error', "Limite de {$nbMax} annonces atteinte. Upgradez votre plan.");
    }

    // ── Validation ────────────────────────────────────────────────────────
    $data = $request->validate([
        'titre'                  => ['required', 'string', 'max:255'],
        'description'            => ['required', 'string', 'min:20'],
        'prix'                   => ['required', 'numeric', 'min:0.01'],
        'unite_prix'             => ['required', 'in:kg,unite,lot,caisse'],
        'type_produit'           => ['required', 'in:fruit,legume,herbe,champignon,autre'],
        'categorie'              => ['required', 'string', 'max:100'],
        'label'                  => ['required', 'in:bio,local,raisonne,conventionnel'],
        'date_recolte'           => ['required', 'date', 'before_or_equal:today'],
        'quantite_disponible'    => ['required', 'numeric', 'min:0.01'],
        'quantite_min_commande'  => ['nullable', 'numeric', 'min:0.01'],
        'localisation'           => ['required', 'string'],
        'ville'                  => ['nullable', 'string', 'max:100'],
        'code_postal'            => ['nullable', 'string', 'max:10'],
        'latitude'               => ['nullable', 'numeric', 'between:-90,90'],
        'longitude'              => ['nullable', 'numeric', 'between:-180,180'],
        'rayon_livraison_km'     => ['nullable', 'integer', 'min:1', 'max:200'],
        'disponible_a_partir_de' => ['nullable', 'date'],
        'disponible_jusqu_a'     => ['nullable', 'date', 'after:disponible_a_partir_de'],
        'livraison_main_propre'  => ['nullable', 'boolean'],
        'livraison_point_relais' => ['nullable', 'boolean'],
        'livraison_domicile'     => ['nullable', 'boolean'],
        'livraison_locker'       => ['nullable', 'boolean'],
        'photos'                 => ['required', 'array', 'max:8'],
        'photos.*'               => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
    ], [
        'description.min'          => 'La description doit faire au moins 20 caractères.',
        'prix.min'                 => 'Le prix doit être supérieur à 0.',
        'quantite_disponible.min'  => 'La quantité doit être supérieure à 0.',
        'disponible_jusqu_a.after' => 'La date de fin doit être après la date de début.',
    ]);

    // ── Au moins un mode de livraison ─────────────────────────────────────
    if (
        empty($request->livraison_main_propre) &&
        empty($request->livraison_point_relais) &&
        empty($request->livraison_domicile) &&
        empty($request->livraison_locker)
    ) {
        return back()->withErrors(['livraison' => 'Sélectionnez au moins un mode de livraison.'])->withInput();
    }

    // ── Création de l'annonce ─────────────────────────────────────────────
    $annonce = Annonce::create(array_merge($data, [
        'user_id'                => Auth::id(),
        'livraison_main_propre'  => $request->boolean('livraison_main_propre'),
        'livraison_point_relais' => $request->boolean('livraison_point_relais'),
        'livraison_domicile'     => $request->boolean('livraison_domicile'),
        'livraison_locker'       => $request->boolean('livraison_locker'),
        'statut'                 => 'disponible',
    ]));

    // ── Upload photos ─────────────────────────────────────────────────────
    if ($request->hasFile('photos')) {
        $this->uploadPhotos($request->file('photos'), $annonce);
    }

    // ── Incrémenter le compteur d'annonces du user ────────────────────────
    Auth::user()->increment('nb_annonces');

    return redirect()
        ->route('annonces.show', $annonce->slug)
        ->with('success', 'Votre annonce a été publiée ! 🌱');
}

    // ── EDIT ───────────────────────────────────────────────────────────────────
    public function edit(Annonce $annonce)
    {
        Gate::authorize('update', $annonce);
        return view('annonces.edit', compact('annonce'));
    }

    // ── UPDATE ─────────────────────────────────────────────────────────────────
    public function update(Request $request, Annonce $annonce)
    {
        Gate::authorize('update', $annonce);

        $data = $request->validate([
            'titre'                  => ['required', 'string', 'max:255'],
            'description'            => ['required', 'string', 'min:20'],
            'prix'                   => ['required', 'numeric', 'min:0.01'],
            'unite_prix'             => ['required', 'in:kg,unite,lot,caisse'],
            'type_produit'           => ['required', 'in:fruit,legume,herbe,champignon,autre'],
            'categorie'              => ['required', 'string', 'max:100'],
            'label'                  => ['required', 'in:bio,local,raisonne,conventionnel'],
            'date_recolte'           => ['required', 'date', 'before_or_equal:today'],
            'quantite_disponible'    => ['required', 'numeric', 'min:0.01'],
            'quantite_min_commande'  => ['nullable', 'numeric', 'min:0.01'],
            'localisation'           => ['required', 'string'],
            'ville'                  => ['nullable', 'string', 'max:100'],
            'code_postal'            => ['nullable', 'string', 'max:10'],
            'latitude'               => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'              => ['nullable', 'numeric', 'between:-180,180'],
            'rayon_livraison_km'     => ['nullable', 'integer', 'min:1', 'max:200'],
            'disponible_a_partir_de' => ['nullable', 'date'],
            'disponible_jusqu_a'     => ['nullable', 'date'],
            'livraison_main_propre'  => ['nullable', 'boolean'],
            'livraison_point_relais' => ['nullable', 'boolean'],
            'livraison_domicile'     => ['nullable', 'boolean'],
            'livraison_locker'       => ['nullable', 'boolean'],
            'statut'                 => ['required', 'in:disponible,brouillon,desactivee'],
            'photos_nouvelles'       => ['nullable', 'array'],
            'photos_nouvelles.*'     => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'photos_supprimer'       => ['nullable', 'array'],
        ]);

        $annonce->update(array_merge($data, [
            'livraison_main_propre'  => $request->boolean('livraison_main_propre'),
            'livraison_point_relais' => $request->boolean('livraison_point_relais'),
            'livraison_domicile'     => $request->boolean('livraison_domicile'),
            'livraison_locker'       => $request->boolean('livraison_locker'),
        ]));

        // Supprimer des photos existantes
        if ($request->filled('photos_supprimer')) {
            $photosActuelles = $annonce->photos ?? [];
            foreach ($request->photos_supprimer as $photoPath) {
                Storage::disk('public')->delete($photoPath);
                $photosActuelles = array_filter($photosActuelles, fn($p) => $p !== $photoPath);
            }
            $annonce->update(['photos' => array_values($photosActuelles)]);
        }

        // Ajouter nouvelles photos
        if ($request->hasFile('photos_nouvelles')) {
            $this->uploadPhotos($request->file('photos_nouvelles'), $annonce, true);
        }

        return redirect()
            ->route('annonces.show', $annonce->slug)
            ->with('success', 'Annonce mise à jour avec succès.');
    }

    // ── DESTROY ────────────────────────────────────────────────────────────────
    public function destroy(Annonce $annonce)
    {
        Gate::authorize('delete', $annonce);

        // Soft delete — les commandes liées restent intactes
        $annonce->delete();
        Auth::user()->decrement('nb_annonces');

        return redirect()
            ->route('annonces.index')
            ->with('success', 'Annonce supprimée.');
    }

    // ── MES ANNONCES ───────────────────────────────────────────────────────────
    public function mesAnnonces()
    {
        $annonces = Annonce::withTrashed()
            ->where('user_id', Auth::id())
            ->with('optionsActives')
            ->latest()
            ->paginate(10);

        return view('annonces.mes-annonces', compact('annonces'));
    }

    // ── TOGGLE LIKE ────────────────────────────────────────────────────────────
    public function toggleLike(Annonce $annonce)
    {
        $user = Auth::user();
        $like = $annonce->likedBy()->toggle($user->id);

        $estLike = count($like['attached']) > 0;
        $annonce->update(['nb_likes' => $annonce->likedBy()->count()]);
 // Notifier le vendeur (seulement si c'est un like, pas un unlike)
        if ($estLike && $annonce->user_id !== Auth::id()) {
            $annonce->user->notify(new \App\Notifications\AnnonceLikee($annonce, Auth::user()));
        }

        if (request()->ajax()) {
            return response()->json([
                'liked'    => $estLike,
                'nb_likes' => $annonce->nb_likes,
            ]);
        }

        return back();
    }

    // ── AUTOCOMPLETE ───────────────────────────────────────────────────────────
    public function autocomplete(Request $request)
    {
        $term = $request->input('term', '');

        $resultats = Annonce::disponibles()
            ->where('titre', 'like', "%$term%")
            ->orWhere('categorie', 'like', "%$term%")
            ->limit(8)
            ->pluck('titre')
            ->unique();

        return response()->json($resultats->values());
    }

    // ── PRIVATE : Upload et optimisation photos ────────────────────────────────
    private function uploadPhotos(array $fichiers, Annonce $annonce, bool $ajouter = false): void
{
    $photos = $ajouter ? ($annonce->photos ?? []) : [];
    $maxPhotos = 8;
    $slots = $maxPhotos - count($photos);

    foreach (array_slice($fichiers, 0, $slots) as $fichier) {
        $filename = 'annonces/' . uniqid('annonce_') . '.' . $fichier->getClientOriginalExtension();
        $fichier->storeAs('annonces', basename($filename), 'public');
        $photos[] = $filename;
    }

    $annonce->update(['photos' => $photos]);
}
}
