<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Infos principales
            $table->string('titre');
            $table->string('slug')->unique();               // URL SEO-friendly
            $table->text('description');
            $table->decimal('prix', 8, 2);                  // prix au kg ou à l'unité
            $table->enum('unite_prix', ['kg', 'unite', 'lot', 'caisse'])->default('kg');

            // Produit
            $table->enum('type_produit', ['fruit', 'legume', 'herbe', 'champignon', 'autre'])
                  ->default('legume');
            $table->string('categorie');                    // ex: tomates, pommes, carottes...
            $table->enum('label', ['bio', 'local', 'raisonne', 'conventionnel'])->default('local');
            $table->date('date_recolte');
            $table->decimal('quantite_disponible', 8, 2);   // en kg ou unités
            $table->decimal('quantite_min_commande', 8, 2)->default(0.5);
            $table->decimal('poids_unitaire', 8, 2)->nullable(); // si vente à l'unité

            // Localisation
            $table->string('localisation');
            $table->string('ville')->nullable();
            $table->string('code_postal', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('rayon_livraison_km')->default(30);

            // Livraison (modes acceptés par le vendeur)
            $table->boolean('livraison_main_propre')->default(true);
            $table->boolean('livraison_point_relais')->default(false);
            $table->boolean('livraison_domicile')->default(false);
            $table->boolean('livraison_locker')->default(false);

            // Photos (JSON array de chemins)
            $table->json('photos')->nullable();

            // Disponibilité
            $table->date('disponible_a_partir_de')->nullable();
            $table->date('disponible_jusqu_a')->nullable();

            // Statut
            $table->enum('statut', [
                'brouillon',
                'disponible',
                'reservee',
                'en_cours',
                'vendue',
                'expiree',
                'desactivee',
                'suspendue',    // suspendue par admin
            ])->default('disponible');

            // Options payantes actives (cache pour affichage)
            $table->boolean('est_mise_en_avant')->default(false);
            $table->boolean('est_epinglee')->default(false);
            $table->timestamp('remontee_at')->nullable();   // date de dernière remontée

            // Stats
            $table->unsignedInteger('nb_vues')->default(0);
            $table->unsignedInteger('nb_likes')->default(0);
            $table->unsignedInteger('nb_commandes')->default(0);
            $table->decimal('note_moyenne', 3, 2)->default(0.00);

            $table->timestamps();
            $table->softDeletes();

            // Index pour les recherches fréquentes
            $table->index(['statut', 'created_at']);
            $table->index(['user_id', 'statut']);
            $table->index(['type_produit', 'statut']);
            $table->index(['latitude', 'longitude']);
            $table->index('est_mise_en_avant');
        });

        // Options payantes sur les annonces
        Schema::create('annonce_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annonce_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'mise_en_avant',    // 1.99€ - highlight visuel 7 jours
                'epinglage',        // 2.99€ - épingle en haut de catégorie
                'remontee',         // 2.49€ - remonte en tête de liste 15 jours
                'prolongation',     // 0.99€ - prolonge l'annonce 30 jours
                'galerie',          // 0.99€ - jusqu'à 8 photos
                'urgent',           // 1.49€ - badge "urgent"
            ]);
            $table->decimal('prix_paye', 6, 2);
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamp('debut_at');
            $table->timestamp('fin_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['annonce_id', 'type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annonce_options');
        Schema::dropIfExists('annonces');
    }
};
