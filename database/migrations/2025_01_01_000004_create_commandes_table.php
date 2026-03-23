<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── COMMANDES ──────────────────────────────────────────────────────────
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();             // ex: BIO-2025-00042

            // Parties
            $table->foreignId('acheteur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vendeur_id')->constrained('users')->cascadeOnDelete();

            // Statut global de la commande
            $table->enum('statut', [
                'en_attente',       // panier validé, paiement pas encore lancé
                'paiement_en_cours',
                'payee',            // paiement Stripe confirmé
                'en_preparation',   // vendeur prépare
                'prete',            // prête à être récupérée / expédiée
                'en_livraison',     // en cours de livraison
                'livree',           // livrée / récupérée
                'terminee',         // acheteur a confirmé réception
                'annulee',
                'remboursee',
                'litige',
            ])->default('en_attente');

            // Montants
            $table->decimal('sous_total', 10, 2);           // total produits HT
            $table->decimal('frais_livraison', 8, 2)->default(0);
            $table->decimal('frais_service', 8, 2)->default(0.99); // commission Biocolis
            $table->decimal('total_ttc', 10, 2);            // total payé par l'acheteur
            $table->decimal('montant_vendeur', 10, 2);      // ce que reçoit le vendeur

            // Paiement Stripe
            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->string('stripe_charge_id')->nullable();
            $table->string('stripe_transfer_id')->nullable(); // transfer vers vendeur
            $table->timestamp('paye_at')->nullable();
            $table->timestamp('rembourse_at')->nullable();
            $table->decimal('montant_rembourse', 10, 2)->default(0);

            // Adresse de livraison (snapshot au moment de la commande)
            $table->string('adresse_livraison')->nullable();
            $table->string('ville_livraison')->nullable();
            $table->string('code_postal_livraison', 10)->nullable();
            $table->decimal('latitude_livraison', 10, 7)->nullable();
            $table->decimal('longitude_livraison', 10, 7)->nullable();

            // Notes
            $table->text('note_acheteur')->nullable();      // note pour le vendeur
            $table->text('note_interne')->nullable();        // note admin

            // Timestamps importants
            $table->timestamp('annule_at')->nullable();
            $table->string('annule_par')->nullable();        // 'acheteur', 'vendeur', 'admin'
            $table->string('motif_annulation')->nullable();
            $table->timestamp('livree_at')->nullable();
            $table->timestamp('terminee_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['acheteur_id', 'statut']);
            $table->index(['vendeur_id', 'statut']);
            $table->index(['statut', 'created_at']);
        });

        // ── LIGNES DE COMMANDE ─────────────────────────────────────────────────
        Schema::create('commande_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annonce_id')->constrained()->restrictOnDelete();

            // Snapshot des infos produit au moment de l'achat
            $table->string('titre_annonce');
            $table->decimal('prix_unitaire', 8, 2);
            $table->string('unite_prix');                   // kg, unité, lot...
            $table->decimal('quantite', 8, 2);
            $table->decimal('sous_total', 10, 2);
            $table->string('photo_annonce')->nullable();    // première photo au moment de l'achat

            $table->timestamps();
        });

        // ── LIVRAISONS ─────────────────────────────────────────────────────────
        Schema::create('livraisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained()->cascadeOnDelete();

            // Mode de livraison choisi par l'acheteur
            $table->enum('mode', [
                'main_propre',      // rendez-vous en personne
                'point_relais',     // dépôt en point relais
                'domicile',         // livraison à domicile
                'locker',           // casier connecté
            ]);

            $table->enum('statut', [
                'en_attente',
                'confirmee',        // vendeur a confirmé le créneau
                'en_route',
                'livree',
                'echec',            // tentative échouée
            ])->default('en_attente');

            // Tarif
            $table->decimal('tarif', 8, 2)->default(0);

            // Pour main_propre et domicile
            $table->timestamp('creneau_debut')->nullable();
            $table->timestamp('creneau_fin')->nullable();
            $table->string('adresse_rdv')->nullable();
            $table->decimal('latitude_rdv', 10, 7)->nullable();
            $table->decimal('longitude_rdv', 10, 7)->nullable();

            // Pour point_relais
            $table->string('point_relais_nom')->nullable();
            $table->string('point_relais_adresse')->nullable();
            $table->string('point_relais_code', 20)->nullable();

            // Pour locker
            $table->string('locker_id')->nullable();
            $table->string('locker_code_acces')->nullable();
            $table->timestamp('locker_disponible_jusqu_at')->nullable();

            // Suivi
            $table->string('numero_suivi')->nullable();
            $table->timestamp('livree_at')->nullable();
            $table->string('signature_url')->nullable();    // photo de confirmation
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livraisons');
        Schema::dropIfExists('commande_lignes');
        Schema::dropIfExists('commandes');
    }
};
