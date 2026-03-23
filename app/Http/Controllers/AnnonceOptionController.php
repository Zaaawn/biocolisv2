<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\AnnonceOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class AnnonceOptionController extends Controller
{
    // ── PAGE BOOST ─────────────────────────────────────────────────────────────
    public function boost(Annonce $annonce)
    {
        Gate::authorize('update', $annonce);

        $options = [
            [
                'type'        => 'mise_en_avant',
                'ico'         => '⭐',
                'titre'       => 'Mise en avant',
                'description' => 'Votre annonce apparaît en haut des résultats avec un badge doré.',
                'tarif'       => AnnonceOption::tarifParType('mise_en_avant'),
                'duree'       => AnnonceOption::dureeParType('mise_en_avant'),
                'actif'       => $annonce->est_mise_en_avant,
            ],
            [
                'type'        => 'epinglage',
                'ico'         => '📌',
                'titre'       => 'Épinglage',
                'description' => 'Votre annonce reste épinglée en haut de la catégorie pendant 30 jours.',
                'tarif'       => AnnonceOption::tarifParType('epinglage'),
                'duree'       => AnnonceOption::dureeParType('epinglage'),
                'actif'       => $annonce->est_epinglee,
            ],
            [
                'type'        => 'remontee',
                'ico'         => '🚀',
                'titre'       => 'Remontée',
                'description' => 'Votre annonce est remontée en haut des résultats comme si elle venait d\'être publiée.',
                'tarif'       => AnnonceOption::tarifParType('remontee'),
                'duree'       => AnnonceOption::dureeParType('remontee'),
                'actif'       => false,
            ],
            [
                'type'        => 'urgent',
                'ico'         => '🔥',
                'titre'       => 'Urgent',
                'description' => 'Badge "Urgent" rouge sur votre annonce — attire l\'attention des acheteurs.',
                'tarif'       => AnnonceOption::tarifParType('urgent'),
                'duree'       => AnnonceOption::dureeParType('urgent'),
                'actif'       => false,
            ],
        ];

        $optionsActives = $annonce->optionsActives()->get();

        return view('options.boost', compact('annonce', 'options', 'optionsActives'));
    }

    // ── PAYER UNE OPTION ───────────────────────────────────────────────────────
    public function payer(Request $request, Annonce $annonce)
    {
        abort_unless($annonce->user_id === Auth::id(), 403);

        $type = $request->validate([
            'type' => ['required', 'in:mise_en_avant,epinglage,remontee,prolongation,urgent'],
        ])['type'];

        $tarif = AnnonceOption::tarifParType($type);
        $duree = AnnonceOption::dureeParType($type);

        $labels = [
            'mise_en_avant' => '⭐ Mise en avant',
            'epinglage'     => '📌 Épinglage',
            'remontee'      => '🚀 Remontée',
            'urgent'        => '🔥 Urgent',
            'prolongation'  => '⏳ Prolongation',
        ];

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items'           => [[
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => [
                        'name' => ($labels[$type] ?? $type) . ' — ' . $annonce->titre,
                        'description' => $duree ? "Valable {$duree} jours" : null,
                    ],
                    'unit_amount' => intval($tarif * 100),
                ],
                'quantity' => 1,
            ]],
            'mode'          => 'payment',
            'customer_email'=> Auth::user()->email,
            'metadata'      => [
                'annonce_id' => $annonce->id,
                'type'       => $type,
                'user_id'    => Auth::id(),
            ],
            'success_url' => route('options.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('options.boost', $annonce->slug),
            'locale'      => 'fr',
        ]);

        return redirect($session->url);
    }

    // ── SUCCESS après paiement ─────────────────────────────────────────────────
    public function success(Request $request)
    {
        $sessionId = $request->input('session_id');
        if (!$sessionId) return redirect()->route('annonces.mes-annonces');

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeSession::retrieve($sessionId);
            $meta    = $session->metadata;

            $annonce = Annonce::find($meta->annonce_id);
            $type    = $meta->type;
            $duree   = AnnonceOption::dureeParType($type);

            // Créer l'option
            AnnonceOption::create([
                'annonce_id'               => $annonce->id,
                'user_id'                  => $meta->user_id,
                'type'                     => $type,
                'prix_paye'                => $session->amount_total / 100,
                'stripe_payment_intent_id' => $session->payment_intent,
                'is_active'                => true,
                'debut_at'                 => now(),
                'fin_at'                   => $duree ? now()->addDays($duree) : null,
            ]);

        } catch (\Exception $e) {
            \Log::error('Option paiement error: ' . $e->getMessage());
            return redirect()->route('annonces.mes-annonces')
                ->with('error', 'Erreur lors de l\'activation de l\'option.');
        }

        return redirect()->route('annonces.mes-annonces')
            ->with('success', 'Option activée avec succès ! 🚀');
    }
}
