<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class AbonnementController extends Controller
{
    // ── PAGE TARIFS ────────────────────────────────────────────────────────────
    public function tarifs()
    {
        $plans = Plan::actifs()->where('cible', 'b2b')->get();
        $abonnementActif = Auth::check()
            ? Auth::user()->abonnements()->where('statut', 'actif')->with('plan')->first()
            : null;

        return view('abonnements.tarifs', compact('plans', 'abonnementActif'));
    }

    // ── SOUSCRIRE ──────────────────────────────────────────────────────────────
    public function souscrire(Request $request, Plan $plan)
    {
        $periodicite = $request->input('periodicite', 'mensuel');
        $tarif = $periodicite === 'annuel' ? $plan->prix_annuel : $plan->prix_mensuel;

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items'           => [[
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => [
                        'name'        => 'Plan ' . $plan->nom . ' — Biocolis B2B',
                        'description' => $periodicite === 'annuel'
                            ? 'Abonnement annuel (2 mois offerts)'
                            : 'Abonnement mensuel',
                    ],
                    'unit_amount'  => intval($tarif * 100),
                ],
                'quantity' => 1,
            ]],
            'mode'          => 'payment', // On utilise payment simple, pas subscription
            'customer_email'=> Auth::user()->email,
            'metadata'      => [
                'plan_id'     => $plan->id,
                'user_id'     => Auth::id(),
                'periodicite' => $periodicite,
                'montant'     => $tarif,
            ],
            'success_url' => route('abonnements.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('abonnements.tarifs'),
            'locale'      => 'fr',
        ]);

        return redirect($session->url);
    }

    // ── SUCCESS ────────────────────────────────────────────────────────────────
    public function success(Request $request)
    {
        $sessionId = $request->input('session_id');
        if (!$sessionId) return redirect()->route('abonnements.tarifs');

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session     = StripeSession::retrieve($sessionId);
            $meta        = $session->metadata;
            $plan        = Plan::find($meta->plan_id);
            $periodicite = $meta->periodicite;

            // Annuler l'abonnement actif précédent
            Abonnement::where('user_id', Auth::id())
                ->where('statut', 'actif')
                ->update(['statut' => 'annule', 'annule_at' => now()]);

            // Créer le nouvel abonnement
            $finAt = $periodicite === 'annuel' ? now()->addYear() : now()->addMonth();

            Abonnement::create([
                'user_id'       => Auth::id(),
                'plan_id'       => $plan->id,
                'statut'        => 'actif',
                'periodicite'   => $periodicite,
                'debut_at'      => now(),
                'fin_at'        => $finAt,
                'prochain_paiement_at' => $finAt,
                'montant'       => $meta->montant,
                'stripe_customer_id' => $session->customer ?? null,
            ]);

            // Mettre à jour le rôle si nécessaire
            if (Auth::user()->role === 'particulier') {
                Auth::user()->update(['role' => 'b2b']);
            }

        } catch (\Exception $e) {
            \Log::error('Abonnement success error: ' . $e->getMessage());
            return redirect()->route('abonnements.tarifs')
                ->with('error', 'Erreur lors de l\'activation de l\'abonnement.');
        }

        return redirect()->route('dashboard')
            ->with('success', '🎉 Bienvenue sur le plan ' . $plan->nom . ' ! Votre abonnement est actif.');
    }

    // ── MON ABONNEMENT ─────────────────────────────────────────────────────────
    public function monAbonnement()
    {
        $abonnement = Auth::user()->abonnements()
            ->with('plan')
            ->latest()
            ->first();

        $plans = Plan::actifs()->where('cible', 'b2b')->get();

        return view('abonnements.mon-abonnement', compact('abonnement', 'plans'));
    }

    // ── ANNULER ────────────────────────────────────────────────────────────────
    public function annuler(Abonnement $abonnement)
    {
        abort_unless($abonnement->user_id === Auth::id(), 403);
        abort_unless($abonnement->statut === 'actif', 403);

        $abonnement->update([
            'statut'    => 'annule',
            'annule_at' => now(),
        ]);

        return redirect()->route('abonnements.mon-abonnement')
            ->with('success', 'Abonnement annulé. Il reste actif jusqu\'au ' . $abonnement->fin_at->format('d/m/Y') . '.');
    }
}
