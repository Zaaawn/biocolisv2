<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    // ── LAISSER UN AVIS ───────────────────────────────────────────────────────
    public function store(Request $request, Commande $commande)
    {
        // Seul l'acheteur peut noter (acheteur note vendeur)
        abort_unless($commande->acheteur_id === Auth::id(), 403);
        abort_unless($commande->statut === 'terminee', 403);

        // Un seul avis par commande
        if ($commande->ratings()->where('auteur_id', Auth::id())->exists()) {
            return back()->with('error', 'Vous avez déjà laissé un avis pour cette commande.');
        }

        $data = $request->validate([
            'note'        => ['required', 'integer', 'min:1', 'max:5'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
            'fraicheur'   => ['nullable', 'integer', 'min:1', 'max:5'],
            'emballage'   => ['nullable', 'integer', 'min:1', 'max:5'],
            'communication' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $criteres = array_filter([
            'fraicheur'     => $data['fraicheur'] ?? null,
            'emballage'     => $data['emballage'] ?? null,
            'communication' => $data['communication'] ?? null,
        ]);

        Rating::create([
            'commande_id' => $commande->id,
            'annonce_id'  => $commande->lignes->first()?->annonce_id,
            'auteur_id'   => Auth::id(),
            'cible_id'    => $commande->vendeur_id,
            'sens'        => 'acheteur_note_vendeur',
            'note'        => $data['note'],
            'commentaire' => $data['commentaire'] ?? null,
            'criteres'    => !empty($criteres) ? $criteres : null,
            'is_visible'  => true,
        ]);

        return back()->with('success', 'Merci pour votre avis ! ⭐');
    }

    // ── RÉPONDRE À UN AVIS (vendeur) ──────────────────────────────────────────
    public function repondre(Request $request, Rating $rating)
    {
        abort_unless($rating->cible_id === Auth::id(), 403);
        abort_unless(is_null($rating->reponse_vendeur), 403); // une seule réponse

        $data = $request->validate([
            'reponse' => ['required', 'string', 'max:500'],
        ]);

        $rating->update([
            'reponse_vendeur' => $data['reponse'],
            'repondu_at'      => now(),
        ]);

        return back()->with('success', 'Réponse publiée.');
    }

    // ── AVIS D'UN VENDEUR (page publique) ────────────────────────────────────
    public function vendeur(\App\Models\User $user)
    {
        $avis = Rating::with(['auteur', 'annonce', 'commande'])
            ->where('cible_id', $user->id)
            ->where('sens', 'acheteur_note_vendeur')
            ->where('is_visible', true)
            ->latest()
            ->paginate(10);

        return view('ratings.vendeur', compact('user', 'avis'));
    }
}
