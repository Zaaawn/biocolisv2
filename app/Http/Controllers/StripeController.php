<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Paiement;
use App\Models\StripeWebhook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Refund;
use Stripe\Webhook;
use Illuminate\Support\Facades\Mail;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // ── DASHBOARD REVENUS VENDEUR ──────────────────────────────────────────────
    public function dashboard()
    {
        $user = Auth::user();

        // Ventes récentes
        $ventes_recentes = Commande::with(['lignes', 'acheteur'])
            ->where('vendeur_id', $user->id)
            ->whereIn('statut', ['payee', 'en_preparation', 'prete', 'en_livraison', 'livree', 'terminee'])
            ->latest()
            ->take(10)
            ->get();

        // Stats
        // Statuts "actifs" = commande payée et en cours
        $statutsActifs = ['payee', 'en_preparation', 'prete', 'en_livraison', 'livree'];

        // En attente = commandes payées non encore terminées
        $en_attente = Commande::where('vendeur_id', $user->id)
            ->whereIn('statut', $statutsActifs)
            ->sum('montant_vendeur');

        // Disponible = commandes terminées (réception confirmée)
        $solde_disponible = Commande::where('vendeur_id', $user->id)
            ->where('statut', 'terminee')
            ->sum('montant_vendeur');

        // Total gagné = en attente + disponible
        $total_gagne = $en_attente + $solde_disponible;

        // Ce mois = toutes commandes payées ce mois (avec ou sans paye_at)
        $ce_mois = Commande::where('vendeur_id', $user->id)
            ->whereIn('statut', array_merge($statutsActifs, ['terminee']))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('montant_vendeur');
        $a_iban          = !empty($user->iban);

        return view('stripe.dashboard', compact(
            'ventes_recentes', 'total_gagne', 'ce_mois',
            'en_attente', 'solde_disponible', 'a_iban'
        ));
    }

    // ── WEBHOOK — Reçoit les événements Stripe ─────────────────────────────────
    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature invalid: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Anti-doublon
        if (StripeWebhook::where('stripe_event_id', $event->id)->exists()) {
            return response()->json(['status' => 'already_processed']);
        }

        $webhook = StripeWebhook::create([
            'stripe_event_id' => $event->id,
            'type'            => $event->type,
            'payload'         => $event->toArray(),
            'statut'          => 'recu',
        ]);

        try {
            match ($event->type) {
                'payment_intent.succeeded'      => $this->onPaiementReussi($event->data->object),
                'payment_intent.payment_failed' => $this->onPaiementEchoue($event->data->object),
                'charge.refunded'               => $this->onRemboursement($event->data->object),
                default                         => null,
            };
            $webhook->update(['statut' => 'traite', 'traite_at' => now()]);
        } catch (\Exception $e) {
            Log::error("Webhook {$event->type} error: " . $e->getMessage());
            $webhook->update(['statut' => 'erreur', 'erreur' => $e->getMessage()]);
        }

        return response()->json(['status' => 'ok']);
    }

    // ── PAIEMENT RÉUSSI ────────────────────────────────────────────────────────
       private function onPaiementReussi(\Stripe\PaymentIntent $pi): void
    {
        $commandes = Commande::where('stripe_payment_intent_id', $pi->id)
            ->orWhere(function ($q) use ($pi) {
                $q->where('statut', 'paiement_en_cours')
                  ->where('acheteur_id', $pi->metadata->acheteur_id ?? 0);
            })->get();
 
        foreach ($commandes as $commande) {
            if ($commande->statut === 'paiement_en_cours') {
                $commande->update([
                    'statut'                   => 'payee',
                    'paye_at'                  => now(),
                    'stripe_payment_intent_id' => $pi->id,
                    'stripe_charge_id'         => $pi->latest_charge,
                ]);
 
                Paiement::create([
                    'commande_id' => $commande->id,
                    'user_id'     => $commande->acheteur_id,
                    'type'        => 'achat',
                    'statut'      => 'succes',
                    'montant'     => $commande->total_ttc,
                    'stripe_id'   => $pi->id,
                    'stripe_type' => 'payment_intent',
                    'traite_at'   => now(),
                ]);
 
                foreach ($commande->lignes as $ligne) {
                    $ligne->annonce?->decrement('quantite_disponible', $ligne->quantite);
                    $ligne->annonce?->increment('nb_commandes');
                }
 
                $vendeur = $commande->vendeur;
                $vendeur->increment('solde_en_attente', $commande->montant_vendeur);
                $vendeur->increment('nb_ventes');
                $commande->acheteur->increment('nb_achats');
 
                // Notifications
                $vendeur->notify(new \App\Notifications\NouvelleVente($commande));
                $commande->acheteur->notify(new \App\Notifications\CommandePayee($commande));
 
                // ✅ Emails
                try {
                    Mail::to($commande->acheteur->email)->send(new \App\Mail\CommandePayeeAcheteur($commande));
                    Mail::to($vendeur->email)->send(new \App\Mail\CommandePayeeVendeur($commande));
                } catch (\Exception $e) {
                    Log::warning('Email commande échoué: ' . $e->getMessage());
                }
            }
        }
    }

    // ── COMMANDE TERMINÉE → libérer le solde vendeur ───────────────────────────
    public static function libererSoldeVendeur(Commande $commande): void
    {
        $vendeur = $commande->vendeur;

        // Déplace de "en_attente" vers "disponible"
        $vendeur->decrement('solde_en_attente', $commande->montant_vendeur);
        $vendeur->increment('solde_disponible', $commande->montant_vendeur);
        $vendeur->increment('total_recu', $commande->montant_vendeur);

        // Enregistrer le mouvement
        Paiement::create([
            'commande_id' => $commande->id,
            'user_id'     => $vendeur->id,
            'type'        => 'credit_vendeur',
            'statut'      => 'succes',
            'montant'     => $commande->montant_vendeur,
            'description' => "Crédit commande {$commande->numero} — en attente de virement",
            'traite_at'   => now(),
        ]);
    }

    // ── PAIEMENT ÉCHOUÉ ────────────────────────────────────────────────────────
    private function onPaiementEchoue(\Stripe\PaymentIntent $pi): void
    {
        Commande::where('stripe_payment_intent_id', $pi->id)
            ->update(['statut' => 'en_attente']);
    }

    // ── REMBOURSEMENT ──────────────────────────────────────────────────────────
    private function onRemboursement(\Stripe\Charge $charge): void
    {
        $commande = Commande::where('stripe_charge_id', $charge->id)->first();
        if (!$commande) return;

        $montantRembourse = $charge->amount_refunded / 100;

        $commande->update([
            'statut'           => 'remboursee',
            'rembourse_at'     => now(),
            'montant_rembourse'=> $montantRembourse,
        ]);

        // Retirer du solde vendeur si déjà crédité
        $commande->vendeur?->decrement('solde_en_attente', $commande->montant_vendeur);
    }

    // ── REMBOURSER (admin) ─────────────────────────────────────────────────────
    public function rembourser(Request $request, Commande $commande)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $montant = $request->input('montant', $commande->total_ttc);

        try {
            Refund::create([
                'charge' => $commande->stripe_charge_id,
                'amount' => intval($montant * 100),
            ]);

            $commande->update([
                'statut'           => 'remboursee',
                'rembourse_at'     => now(),
                'montant_rembourse'=> $montant,
            ]);

            return back()->with('success', "Remboursement de {$montant}€ effectué.");
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }
}