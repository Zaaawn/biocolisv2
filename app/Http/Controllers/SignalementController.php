<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Signalement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignalementController extends Controller
{
    // ── SOUMETTRE UN SIGNALEMENT ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'cible_type'  => ['required', 'in:annonce,user'],
            'cible_id'    => ['required', 'integer'],
            'motif'       => ['required', 'in:contenu_inapproprie,arnaque,faux_produit,spam,autre'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $cibleType = $data['cible_type'] === 'annonce' ? Annonce::class : User::class;

        if ($data['cible_type'] === 'annonce') {
            abort_unless(Annonce::find($data['cible_id']), 404);
        } else {
            abort_unless(User::find($data['cible_id']), 404);
            abort_if($data['cible_id'] == Auth::id(), 403);
        }

        // Un seul signalement actif par user/cible
        $existe = Signalement::where('auteur_id', Auth::id())
            ->where('cible_type', $cibleType)
            ->where('cible_id', $data['cible_id'])
            ->where('statut', 'en_attente')
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà signalé cet élément. Notre équipe le traite sous 48h.'
            ], 422);
        }

        Signalement::create([
            'auteur_id'   => Auth::id(),
            'cible_type'  => $cibleType,
            'cible_id'    => $data['cible_id'],
            'motif'       => $data['motif'],
            'description' => $data['description'] ?? null,
            'statut'      => 'en_attente',
        ]);

        // ✅ Toujours retourner du JSON
        return response()->json([
            'success' => true,
            'message' => 'Signalement envoyé. Notre équipe va examiner cela sous 48h.'
        ]);
    }

    // ── LISTE ADMIN ────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $statut = $request->input('statut', 'en_attente');

        $signalements = Signalement::with(['auteur', 'traitePar'])
            ->when($statut !== 'tous', fn($q) => $q->where('statut', $statut))
            ->latest()
            ->paginate(20);

        $signalements->each(function ($sig) {
            try { $sig->cible_resolved = $sig->cible; }
            catch (\Exception $e) { $sig->cible_resolved = null; }
        });

        $stats = [
            'en_attente' => Signalement::where('statut', 'en_attente')->count(),
            'traite'     => Signalement::where('statut', 'traite')->count(),
            'rejete'     => Signalement::where('statut', 'rejete')->count(),
        ];

        return view('signalements.index', compact('signalements', 'statut', 'stats'));
    }

    // ── TRAITER (admin) ────────────────────────────────────────────────────────
    public function traiter(Request $request, Signalement $signalement)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'action'                 => ['required', 'in:traite,rejete'],
            'note_admin'             => ['nullable', 'string', 'max:500'],
            'action_supplementaire'  => ['nullable', 'in:supprimer_annonce,bannir_user'],
        ]);

        $signalement->update([
            'statut'     => $data['action'],
            'note_admin' => $data['note_admin'] ?? null,
            'traite_par' => Auth::id(),
            'traite_at'  => now(),
        ]);

        if ($data['action'] === 'traite' && !empty($data['action_supplementaire'])) {
            match ($data['action_supplementaire']) {
                'supprimer_annonce' => Annonce::find($signalement->cible_id)?->delete(),
                'bannir_user'       => User::find($signalement->cible_id)?->update([
                    'banned_at'  => now(),
                    'ban_reason' => 'Signalement validé : ' . $signalement->motif,
                ]),
            };
        }

        $msg = $data['action'] === 'traite' ? 'traité ✅' : 'rejeté ❌';
        return back()->with('success', "Signalement {$msg}.");
    }
}