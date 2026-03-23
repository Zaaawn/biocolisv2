<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\PanierLigne;
use App\Models\Livraison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PanierController extends Controller
{
    // ── INDEX — Affiche le panier ──────────────────────────────────────────────
    public function index()
    {
        $lignes = PanierLigne::with(['annonce.user'])
            ->where('user_id', Auth::id())
            ->get();

        // Vérifier la disponibilité de chaque article
        $lignes = $lignes->map(function ($ligne) {
            $ligne->disponible = $ligne->annonce
                && $ligne->annonce->isDisponible()
                && $ligne->annonce->quantite_disponible >= $ligne->quantite;
            return $ligne;
        });

        $sousTotal      = $lignes->sum('sous_total');
        $fraisLivraison = $lignes->sum('frais_livraison');
        $fraisService   = 0.99;
        $totalTTC       = $sousTotal + $fraisLivraison + $fraisService;

        return view('panier.index', compact(
            'lignes', 'sousTotal', 'fraisLivraison', 'fraisService', 'totalTTC'
        ));
    }

    // ── AJOUTER ────────────────────────────────────────────────────────────────
    public function ajouter(Request $request, Annonce $annonce)
    {
        // Pas sa propre annonce
        if ($annonce->user_id === Auth::id()) {
            return $this->reponse($request, false, 'Vous ne pouvez pas acheter votre propre annonce.');
        }

        // Annonce disponible ?
        if (!$annonce->isDisponible()) {
            return $this->reponse($request, false, 'Cette annonce n\'est plus disponible.');
        }

        $quantite     = max(
            $annonce->quantite_min_commande ?? 0.5,
            floatval($request->input('quantite', 1))
        );
        $modeLivraison = $request->input('mode_livraison', 'main_propre');

        // Vérifier que le mode livraison est accepté par le vendeur
        $modesAcceptes = $annonce->modes_livraison;
        if (!in_array($modeLivraison, $modesAcceptes)) {
            $modeLivraison = $modesAcceptes[0] ?? 'main_propre';
        }

        // Quantité dispo ?
        if ($quantite > $annonce->quantite_disponible) {
            return $this->reponse($request, false, "Quantité maximum disponible : {$annonce->quantite_disponible} {$annonce->unite_prix}");
        }

        // Upsert en BDD
        PanierLigne::updateOrCreate(
            ['user_id' => Auth::id(), 'annonce_id' => $annonce->id],
            ['quantite' => $quantite, 'mode_livraison' => $modeLivraison]
        );

        $nbArticles = PanierLigne::where('user_id', Auth::id())->count();

        return $this->reponse($request, true, 'Ajouté au panier !', [
            'nb_articles' => $nbArticles,
        ]);
    }

    // ── METTRE À JOUR (quantité ou mode livraison) ─────────────────────────────
    public function mettreAJour(Request $request, PanierLigne $ligne)
    {
        // Sécurité — c'est bien la ligne de cet utilisateur
        abort_unless($ligne->user_id === Auth::id(), 403);

        $data = $request->validate([
            'quantite'      => ['nullable', 'numeric', 'min:0.1'],
            'mode_livraison'=> ['nullable', 'in:main_propre,point_relais,domicile,locker'],
        ]);

        $annonce = $ligne->annonce;

        if (isset($data['quantite'])) {
            if ($data['quantite'] > $annonce->quantite_disponible) {
                return response()->json([
                    'success' => false,
                    'message' => "Max : {$annonce->quantite_disponible} {$annonce->unite_prix}",
                ], 422);
            }
            $ligne->quantite = $data['quantite'];
        }

        if (isset($data['mode_livraison'])) {
            $ligne->mode_livraison = $data['mode_livraison'];
        }

        $ligne->save();

        // Recalcule les totaux
        $totaux = $this->calculerTotaux(Auth::id());

        return response()->json([
            'success'         => true,
            'sous_total_ligne' => number_format($ligne->sous_total, 2, ',', ' '),
            'frais_livraison_ligne' => number_format($ligne->frais_livraison, 2, ',', ' '),
            ...$totaux,
        ]);
    }

    // ── SUPPRIMER ──────────────────────────────────────────────────────────────
    public function supprimer(PanierLigne $ligne)
    {
        abort_unless($ligne->user_id === Auth::id(), 403);
        $ligne->delete();

        $totaux     = $this->calculerTotaux(Auth::id());
        $nbArticles = PanierLigne::where('user_id', Auth::id())->count();

        if (request()->ajax()) {
            return response()->json([
                'success'    => true,
                'nb_articles'=> $nbArticles,
                ...$totaux,
            ]);
        }

        return redirect()->route('panier.index')->with('success', 'Article retiré du panier.');
    }

    // ── VIDER ──────────────────────────────────────────────────────────────────
    public function vider()
    {
        PanierLigne::where('user_id', Auth::id())->delete();
        return redirect()->route('panier.index')->with('success', 'Panier vidé.');
    }

    // ── CHECKOUT — Récap avant paiement ───────────────────────────────────────
    public function checkout()
    {
        $lignes = PanierLigne::with(['annonce.user'])
            ->where('user_id', Auth::id())
            ->get();

        if ($lignes->isEmpty()) {
            return redirect()->route('panier.index')
                ->with('error', 'Votre panier est vide.');
        }

        // Vérifier que tout est encore disponible
        foreach ($lignes as $ligne) {
            if (!$ligne->annonce || !$ligne->annonce->isDisponible()) {
                return redirect()->route('panier.index')
                    ->with('error', "L'annonce \"{$ligne->annonce?->titre}\" n'est plus disponible.");
            }
            if ($ligne->quantite > $ligne->annonce->quantite_disponible) {
                return redirect()->route('panier.index')
                    ->with('error', "Stock insuffisant pour \"{$ligne->annonce->titre}\".");
            }
        }

        // Grouper par vendeur (une commande par vendeur)
        $groupes = $lignes->groupBy('annonce.user_id');

        $sousTotal      = $lignes->sum('sous_total');
        $fraisLivraison = $lignes->sum('frais_livraison');
        $fraisService   = 0.99;
        $totalTTC       = $sousTotal + $fraisLivraison + $fraisService;

        return view('panier.checkout', compact(
            'lignes', 'groupes', 'sousTotal', 'fraisLivraison', 'fraisService', 'totalTTC'
        ));
    }

    // ── COMPTEUR PANIER (pour le header) ──────────────────────────────────────
    public function compteur()
    {
        $nb = Auth::check()
            ? PanierLigne::where('user_id', Auth::id())->count()
            : 0;

        return response()->json(['nb' => $nb]);
    }

    // ── PRIVATE HELPERS ────────────────────────────────────────────────────────
    private function calculerTotaux(int $userId): array
    {
        $lignes = PanierLigne::with('annonce')->where('user_id', $userId)->get();

        $sousTotal      = $lignes->sum('sous_total');
        $fraisLivraison = $lignes->sum('frais_livraison');
        $fraisService   = $lignes->isEmpty() ? 0 : 0.99;
        $totalTTC       = $sousTotal + $fraisLivraison + $fraisService;

        return [
            'sous_total'       => number_format($sousTotal, 2, ',', ' ') . ' €',
            'frais_livraison'  => number_format($fraisLivraison, 2, ',', ' ') . ' €',
            'frais_service'    => number_format($fraisService, 2, ',', ' ') . ' €',
            'total_ttc'        => number_format($totalTTC, 2, ',', ' ') . ' €',
            'nb_articles'      => $lignes->count(),
        ];
    }

    private function reponse(Request $request, bool $success, string $message, array $extra = [])
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(array_merge(['success' => $success, 'message' => $message], $extra));
        }

        return $success
            ? back()->with('success', $message)
            : back()->with('error', $message);
    }
}
