<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Commande;
use App\Models\CommandeLigne;
use App\Models\Livraison;
use App\Models\PanierLigne;
use App\Models\Paiement;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CommandeController extends Controller
{
    // ── CRÉER COMMANDE + SESSION STRIPE ───────────────────────────────────────
    public function creerDepuisPanier(Request $request)
    {
        $lignes = PanierLigne::with(['annonce.user'])
            ->where('user_id', Auth::id())
            ->get();

        if ($lignes->isEmpty()) {
            return redirect()->route('panier.index')
                ->with('error', 'Votre panier est vide.');
        }

        // Vérification finale disponibilité
        foreach ($lignes as $ligne) {
            if (!$ligne->annonce?->isDisponible()) {
                return redirect()->route('panier.index')
                    ->with('error', "L'annonce \"{$ligne->annonce?->titre}\" n'est plus disponible.");
            }
        }

        // Grouper par vendeur → une commande par vendeur
        $groupes = $lignes->groupBy(fn($l) => $l->annonce->user_id);

        DB::beginTransaction();
        try {
            $commandes  = [];
            $lineItems  = [];
            $fraisService = 0.99;

            foreach ($groupes as $vendeurId => $lignesVendeur) {
                $sousTotal      = $lignesVendeur->sum('sous_total');
                $fraisLivraison = $lignesVendeur->sum('frais_livraison');
                $totalTTC       = $sousTotal + $fraisLivraison + $fraisService;
               $commissionPct = $vendeur->abonnementActif?->plan->commission_pct ?? 12;
$montantVendeur = round($sousTotal * (1 - $commissionPct / 100), 2); 

                // Créer la commande
                $commande = Commande::create([
                    'acheteur_id'    => Auth::id(),
                    'vendeur_id'     => $vendeurId,
                    'statut'         => 'paiement_en_cours',
                    'sous_total'     => $sousTotal,
                    'frais_livraison'=> $fraisLivraison,
                    'frais_service'  => $fraisService,
                    'total_ttc'      => $totalTTC,
                    'montant_vendeur'=> $montantVendeur,
                    'adresse_livraison'   => Auth::user()->adresse,
                    'ville_livraison'     => null,
                    'code_postal_livraison' => null,
                ]);

                // Créer les lignes
                foreach ($lignesVendeur as $ligne) {
                    CommandeLigne::create([
                        'commande_id'   => $commande->id,
                        'annonce_id'    => $ligne->annonce_id,
                        'titre_annonce' => $ligne->annonce->titre,
                        'prix_unitaire' => $ligne->annonce->prix,
                        'unite_prix'    => $ligne->annonce->unite_prix,
                        'quantite'      => $ligne->quantite,
                        'sous_total'    => $ligne->sous_total,
                        'photo_annonce' => (is_array($ligne->annonce->photos) && !empty($ligne->annonce->photos)) ? $ligne->annonce->photos[0] : null,
                    ]);
                }

                // Créer la livraison
                Livraison::create([
                    'commande_id' => $commande->id,
                    'mode'        => $lignesVendeur->first()->mode_livraison,
                    'tarif'       => $fraisLivraison,
                    'statut'      => 'en_attente',
                ]);

                $commandes[] = $commande;

                // Préparer les line items Stripe
                // ✅ Stripe n'accepte que des quantités entières
                // On multiplie le prix par la quantité pour gérer les décimales (1.5 kg etc.)
                foreach ($lignesVendeur as $ligne) {
                    $montantTotal = round($ligne->annonce->prix * $ligne->quantite, 2);
                    $lineItems[] = [
                        'price_data' => [
                            'currency'     => 'eur',
                            'product_data' => [
                                'name'   => $ligne->annonce->titre . ' (' . $ligne->quantite . ' ' . $ligne->annonce->unite_prix . ')',
                                'images' => ($ligne->annonce->photos && count($ligne->annonce->photos) > 0)
                                    ? [asset('storage/' . $ligne->annonce->photos[0])]
                                    : [],
                            ],
                            'unit_amount'  => intval(round($montantTotal * 100)),
                        ],
                        'quantity' => 1, // Toujours 1 — le montant intègre déjà la quantité
                    ];
                }

                // Frais livraison dans Stripe si > 0
                if ($fraisLivraison > 0) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency'     => 'eur',
                            'product_data' => ['name' => 'Frais de livraison'],
                            'unit_amount'  => intval($fraisLivraison * 100),
                        ],
                        'quantity' => 1,
                    ];
                }
            }

            // Frais de service (une seule fois)
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => ['name' => 'Frais de service Biocolis'],
                    'unit_amount'  => intval($fraisService * 100),
                ],
                'quantity' => 1,
            ];

            // IDs des commandes pour le webhook
            $commandeIds = collect($commandes)->pluck('id')->join(',');

            // Session Stripe Checkout
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'customer_email'       => Auth::user()->email,
                'metadata'             => [
                    'commande_ids' => $commandeIds,
                    'acheteur_id'  => Auth::id(),
                ],
                'success_url' => route('commandes.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('commandes.cancel'),
                'locale'      => 'fr',
            ]);

            // Stocker le payment intent sur chaque commande
            foreach ($commandes as $commande) {
                $commande->update([
                    'stripe_payment_intent_id' => $session->payment_intent,
                ]);
            }

            DB::commit();

            // Vider le panier
            PanierLigne::where('user_id', Auth::id())->delete();

            return redirect($session->url);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur création commande: ' . $e->getMessage());
            return redirect()->route('panier.checkout')
                ->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    // ── SUCCESS (après paiement Stripe) ───────────────────────────────────────
       public function success(Request $request)
    {
        $sessionId = $request->input('session_id');
        if (!$sessionId) return redirect()->route('annonces.index');
 
        Stripe::setApiKey(config('services.stripe.secret'));
        try {
            $session     = StripeSession::retrieve($sessionId);
            $commandeIds = explode(',', $session->metadata->commande_ids ?? '');
 
            $commandes = Commande::with(['lignes', 'livraison', 'vendeur'])
                ->whereIn('id', $commandeIds)
                ->where('acheteur_id', Auth::id())
                ->get();
 
            $acheteurDejaIncremente = false;
            foreach ($commandes as $commande) {
                if ($commande->statut === 'paiement_en_cours') {
                    $commande->update([
                        'statut'                   => 'payee',
                        'paye_at'                  => now(),
                        'stripe_payment_intent_id' => $session->payment_intent,
                    ]);
 
                    foreach ($commande->lignes as $ligne) {
                        $ligne->annonce?->decrement('quantite_disponible', $ligne->quantite);
                        $ligne->annonce?->increment('nb_commandes');
                    }
 
                    $commande->vendeur->increment('nb_ventes');
                    $commande->vendeur->increment('solde_en_attente', $commande->montant_vendeur);
 
                    if (!$acheteurDejaIncremente) {
                        Auth::user()->increment('nb_achats');
                        $acheteurDejaIncremente = true;
                    }
 
                    // ✅ Emails
                    try {
                        Mail::to($commande->acheteur->email)->send(new \App\Mail\CommandePayeeAcheteur($commande));
                        Mail::to($commande->vendeur->email)->send(new \App\Mail\CommandePayeeVendeur($commande));
                    } catch (\Exception $e) {
                        \Log::warning('Email commande success échoué: ' . $e->getMessage());
                    }
                }
            }
 
        } catch (\Exception $e) {
            \Log::error('Erreur success paiement: ' . $e->getMessage());
            $commandes = collect();
        }
 
        return view('commandes.success', compact('commandes'));
    }

    // ── CANCEL ────────────────────────────────────────────────────────────────
    public function cancel()
    {
        // Remettre les commandes en attente
        Commande::where('acheteur_id', Auth::id())
            ->where('statut', 'paiement_en_cours')
            ->update(['statut' => 'en_attente']);

        return redirect()->route('panier.checkout')
            ->with('warning', 'Paiement annulé. Votre panier a été conservé.');
    }

    // ── MES COMMANDES ─────────────────────────────────────────────────────────
    public function mesCommandes()
    {
        $commandes = Commande::with(['lignes', 'livraison', 'vendeur'])
            ->where('acheteur_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('commandes.mes-commandes', compact('commandes'));
    }

    // ── MES VENTES ────────────────────────────────────────────────────────────
    public function mesVentes()
    {
        $commandes = Commande::with(['lignes', 'livraison', 'acheteur'])
            ->where('vendeur_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('commandes.mes-ventes', compact('commandes'));
    }

    // ── DÉTAIL COMMANDE ───────────────────────────────────────────────────────
    public function show(Commande $commande)
    {
        // Seuls l'acheteur, le vendeur et l'admin peuvent voir
        abort_unless(
            $commande->acheteur_id === Auth::id() ||
            $commande->vendeur_id === Auth::id() ||
            Auth::user()->isAdmin(),
            403
        );

        $commande->load(['lignes.annonce', 'livraison', 'acheteur', 'vendeur', 'ratings']);

        return view('commandes.show', compact('commande'));
    }

    // ── ANNULER COMMANDE ──────────────────────────────────────────────────────
    // ── ANNULER ───────────────────────────────────────────────────────────────
    public function annuler(Request $request, Commande $commande)
    {
        abort_unless($commande->acheteur_id === Auth::id(), 403);
 
        if (!$commande->estAnnulable()) {
            return back()->with('error', 'Cette commande ne peut plus être annulée.');
        }
 
        $commande->changerStatut('annulee');
        $commande->update([
            'annule_par'       => 'acheteur',
            'motif_annulation' => $request->input('motif', 'Annulation par l\'acheteur'),
        ]);
 
        // Remboursement Stripe automatique
        if ($commande->stripe_charge_id) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                \Stripe\Refund::create(['charge' => $commande->stripe_charge_id]);
            } catch (\Exception $e) {
                \Log::error('Remboursement Stripe échoué: ' . $e->getMessage());
            }
        }
 
        // ✅ Email annulation
        try {
            Mail::to($commande->acheteur->email)->send(new \App\Mail\CommandeAnnulee($commande));
        } catch (\Exception $e) {
            \Log::warning('Email annulation échoué: ' . $e->getMessage());
        }
 
        return back()->with('success', 'Commande annulée.');
    }
 

    // ── CONFIRMER RÉCEPTION ───────────────────────────────────────────────────
    public function confirmerReception(Commande $commande)
    {
        abort_unless($commande->acheteur_id === Auth::id(), 403);
        abort_unless($commande->statut === 'livree', 403);

        $commande->changerStatut('terminee');

        return redirect()
            ->route('commandes.show', $commande->id)
            ->with('success', 'Réception confirmée ! Vous pouvez maintenant laisser un avis. ⭐');
    }

    // ── CHANGER STATUT (vendeur) ──────────────────────────────────────────────
   public function changerStatut(Request $request, Commande $commande)
    {
        abort_unless($commande->vendeur_id === Auth::id(), 403);
 
        $statut = $request->validate([
            'statut' => ['required', 'in:en_preparation,prete,en_livraison,livree'],
        ])['statut'];
 
        $commande->changerStatut($statut);
 
        // Notifications
        if ($statut === 'livree') {
            $commande->acheteur->notify(new \App\Notifications\CommandeLivree($commande));
        } else {
            $commande->acheteur->notify(new \App\Notifications\StatutCommandeChange($commande, $statut));
        }
 
        // ✅ Email changement de statut
        try {
            Mail::to($commande->acheteur->email)->send(new \App\Mail\CommandeStatutChange($commande, $statut));
        } catch (\Exception $e) {
            \Log::warning('Email statut commande échoué: ' . $e->getMessage());
        }
 
        return back()->with('success', 'Statut mis à jour.');
    }
}