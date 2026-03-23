<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Commande;
use App\Models\Conversation;
use App\Models\PanierLigne;
use App\Models\Paiement;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Données communes
        $data = [
            'user'        => $user,
            'nb_messages' => Conversation::pourUser($user->id)->get()->sum('non_lus_pour_moi'),
            'nb_panier'   => PanierLigne::where('user_id', $user->id)->count(),
        ];

        return match ($user->role) {
            'admin'         => $this->dashboardAdmin($data),
            'b2b'           => $this->dashboardB2B($data),
            'professionnel' => $this->dashboardVendeur($data),
            default         => $this->dashboardParticulier($data),
        };
    }

    // ── DASHBOARD PARTICULIER ──────────────────────────────────────────────────
    private function dashboardParticulier(array $data)
    {
        $user = $data['user'];

        // Commandes récentes (en tant qu'acheteur)
        $data['commandes_recentes'] = Commande::with(['lignes', 'vendeur', 'livraison'])
            ->where('acheteur_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Ventes récentes (en tant que vendeur)
        $data['ventes_recentes'] = Commande::with(['lignes', 'acheteur'])
            ->where('vendeur_id', $user->id)
            ->whereNotNull('paye_at')
            ->latest()
            ->take(3)
            ->get();

        // Mes annonces actives
        $data['mes_annonces'] = Annonce::where('user_id', $user->id)
            ->where('statut', 'disponible')
            ->latest()
            ->take(4)
            ->get();

        // Favoris récents
        $data['favoris'] = $user->likes()
            ->with('user')
            ->latest('likes.created_at')
            ->take(4)
            ->get();

        // ✅ Stats : gains ET achats
        $data['stats'] = [
            'nb_achats'       => Commande::where('acheteur_id', $user->id)->whereNotNull('paye_at')->count(),
            'nb_annonces'     => Annonce::where('user_id', $user->id)->where('statut', 'disponible')->count(),
            'nb_favoris'      => $user->likes()->count(),
            'total_gagne'     => Commande::where('vendeur_id', $user->id)->whereNotNull('paye_at')->sum('montant_vendeur'),
            'nb_ventes'       => Commande::where('vendeur_id', $user->id)->whereNotNull('paye_at')->count(),
            'solde_disponible'=> Commande::where('vendeur_id', $user->id)->where('statut', 'terminee')->sum('montant_vendeur'),
            'solde_en_attente'=> Commande::where('vendeur_id', $user->id)->whereIn('statut', ['payee','en_preparation','prete','en_livraison','livree'])->sum('montant_vendeur'),
        ];

        // Avis à laisser
        $data['avis_a_laisser'] = Commande::where('acheteur_id', $user->id)
            ->where('statut', 'terminee')
            ->whereDoesntHave('ratings', fn($q) => $q->where('auteur_id', $user->id))
            ->with(['lignes', 'vendeur'])
            ->take(3)
            ->get();

        return view('dashboard.particulier', $data);
    }

    // ── DASHBOARD VENDEUR / PROFESSIONNEL ──────────────────────────────────────
    private function dashboardVendeur(array $data)
    {
        $user = $data['user'];

        // Stats financières
        $data['stats'] = [
            'total_gagne'     => Commande::where('vendeur_id', $user->id)->whereNotNull('paye_at')->sum('montant_vendeur'),
            'ce_mois'         => Commande::where('vendeur_id', $user->id)->whereNotNull('paye_at')
                                    ->whereMonth('paye_at', now()->month)->sum('montant_vendeur'),
            'nb_ventes'       => $user->nb_ventes,
            'nb_annonces'     => $user->nb_annonces,
            'note_moyenne'    => $user->note_moyenne,
            'nb_avis'         => $user->nb_avis,
            'en_attente_paiement' => Commande::where('vendeur_id', $user->id)
                                    ->where('statut', 'payee')->sum('montant_vendeur'),
        ];

        // Commandes à traiter (payées et non livrées)
        $data['commandes_a_traiter'] = Commande::with(['lignes', 'acheteur', 'livraison'])
            ->where('vendeur_id', $user->id)
            ->whereIn('statut', ['payee', 'en_preparation', 'prete', 'en_livraison'])
            ->latest()
            ->get();

        // Mes annonces
        $data['mes_annonces'] = Annonce::where('user_id', $user->id)
            ->withCount('panierLignes')
            ->latest()
            ->take(6)
            ->get();

        // Revenus par mois (6 derniers mois)
        $data['revenus_mensuels'] = Commande::where('vendeur_id', $user->id)
            ->whereNotNull('paye_at')
            ->where('paye_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(paye_at, '%Y-%m') as mois, SUM(montant_vendeur) as total")
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois');

        // Derniers avis reçus
        $data['avis_recents'] = Rating::where('cible_id', $user->id)
            ->where('sens', 'acheteur_note_vendeur')
            ->with('auteur')
            ->latest()
            ->take(3)
            ->get();

        // Stripe status
        $data['stripe_actif'] = $user->hasStripeAccount();

        return view('dashboard.vendeur', $data);
    }

    // ── DASHBOARD B2B ─────────────────────────────────────────────────────────
    private function dashboardB2B(array $data)
    {
        $user = $data['user'];

        $data['stats'] = [
            'total_depense'  => Commande::where('acheteur_id', $user->id)->whereNotNull('paye_at')->sum('total_ttc'),
            'ce_mois'        => Commande::where('acheteur_id', $user->id)->whereNotNull('paye_at')
                                    ->whereMonth('paye_at', now()->month)->sum('total_ttc'),
            'nb_commandes'   => $user->nb_achats,
            'nb_producteurs' => Commande::where('acheteur_id', $user->id)
                                    ->distinct('vendeur_id')->count('vendeur_id'),
        ];

        $data['commandes_recentes'] = Commande::with(['lignes', 'vendeur', 'livraison'])
            ->where('acheteur_id', $user->id)
            ->latest()
            ->take(8)
            ->get();

        // Achats mensuels
        $data['achats_mensuels'] = Commande::where('acheteur_id', $user->id)
            ->whereNotNull('paye_at')
            ->where('paye_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(paye_at, '%Y-%m') as mois, SUM(total_ttc) as total")
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois');

        $data['abonnement'] = $user->abonnementActif?->load('plan');

        return view('dashboard.b2b', $data);
    }

    // ── DASHBOARD ADMIN ───────────────────────────────────────────────────────
    private function dashboardAdmin(array $data)
    {
        // Stats globales plateforme
        $data['stats'] = [
            'nb_users'        => \App\Models\User::count(),
            'nb_annonces'     => Annonce::count(),
            'nb_commandes'    => Commande::count(),
            'gmv_total'       => Commande::whereNotNull('paye_at')->sum('total_ttc'),
            'revenus_biocolis'=> Commande::whereNotNull('paye_at')
                ->selectRaw('SUM(total_ttc - montant_vendeur) as commission')
                ->value('commission') ?? 0,
            'ce_mois_gmv'     => Commande::whereNotNull('paye_at')
                ->whereMonth('paye_at', now()->month)->sum('total_ttc'),
            'nb_signalements' => \App\Models\Signalement::where('statut', 'en_attente')->count(),
            'nb_stripe_actif' => \App\Models\User::where('stripe_account_status', 'actif')->count(),
        ];

        // Évolution GMV mensuelle
        $data['gmv_mensuel'] = Commande::whereNotNull('paye_at')
            ->where('paye_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(paye_at, '%Y-%m') as mois, SUM(total_ttc) as total")
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois');

        // Derniers utilisateurs inscrits
        $data['nouveaux_users'] = \App\Models\User::latest()->take(5)->get();

        // Dernières commandes
        $data['dernieres_commandes'] = Commande::with(['acheteur', 'vendeur'])
            ->latest()->take(8)->get();

        // Signalements en attente
        $data['signalements'] = \App\Models\Signalement::with(['auteur', 'cible'])
            ->where('statut', 'en_attente')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.admin', $data);
    }
}