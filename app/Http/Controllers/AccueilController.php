<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Commande;
use App\Models\User;

class AccueilController extends Controller
{
    public function index()
    {
        // Annonces récentes
        $annonces_recentes = Annonce::with(['user', 'optionsActives'])
            ->disponibles()
            ->latest()
            ->take(8)
            ->get();

        // Annonces mises en avant
        $annonces_vedettes = Annonce::with(['user', 'optionsActives'])
            ->disponibles()
            ->where('est_mise_en_avant', true)
            ->take(4)
            ->get();

        // Stats plateforme
        $stats = [
            'nb_annonces'   => Annonce::disponibles()->count(),
            'nb_producteurs'=> User::whereIn('role', ['professionnel', 'b2b'])->count(),
            'nb_commandes'  => Commande::whereNotNull('paye_at')->count(),
            'nb_villes'     => Annonce::disponibles()
                ->whereNotNull('ville')
                ->distinct('ville')
                ->count('ville'),
        ];

        return view('welcome', compact('annonces_recentes', 'annonces_vedettes', 'stats'));
    }
}
