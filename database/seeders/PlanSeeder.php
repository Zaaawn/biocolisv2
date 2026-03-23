<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'nom'            => 'Starter',
                'slug'           => 'starter',
                'cible'          => 'b2b',
                'prix_mensuel'   => 29.00,
                'prix_annuel'    => 290.00, // 2 mois offerts
                'nb_annonces_max'=> 10,
                'commission_pct' => 10, // 2% de moins que le standard
                'livraison_incluse' => false,
                'is_active'      => true,
                'ordre'          => 1,
                'fonctionnalites'=> [
                    '10 annonces actives simultanées',
                    'Commission réduite à 10%',
                    'Badge "Premium" sur le profil',
                    'Statistiques de base',
                    'Support par email',
                ],
            ],
            [
                'nom'            => 'Business',
                'slug'           => 'business',
                'cible'          => 'b2b',
                'prix_mensuel'   => 79.00,
                'prix_annuel'    => 790.00,
                'nb_annonces_max'=> 50,
                'commission_pct' => 8,
                'livraison_incluse' => true,
                'is_active'      => true,
                'ordre'          => 2,
                'fonctionnalites'=> [
                    '50 annonces actives simultanées',
                    'Commission réduite à 8%',
                    'Badge "Business" sur le profil',
                    'Statistiques avancées',
                    '1 mise en avant offerte / mois',
                    'Support prioritaire',
                    'Livraison incluse dans les frais',
                ],
            ],
            [
                'nom'            => 'Premium',
                'slug'           => 'premium',
                'cible'          => 'b2b',
                'prix_mensuel'   => 149.00,
                'prix_annuel'    => 1490.00,
                'nb_annonces_max'=> 0, // illimité
                'commission_pct' => 5,
                'livraison_incluse' => true,
                'is_active'      => true,
                'ordre'          => 3,
                'fonctionnalites'=> [
                    'Annonces illimitées',
                    'Commission réduite à 5%',
                    'Badge "Pro" doré sur le profil',
                    'Tableau de bord analytics complet',
                    '3 mises en avant offertes / mois',
                    'Support dédié 7j/7',
                    'Livraison incluse dans les frais',
                    'Compte manager dédié',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        $this->command->info('✅ Plans B2B créés : Starter, Business, Premium');
    }
}
