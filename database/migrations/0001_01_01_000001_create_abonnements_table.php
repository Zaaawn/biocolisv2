<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Plans disponibles (définis en dur, pas dynamique)
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('nom');                          // ex: "Starter", "Business", "Premium"
            $table->string('slug')->unique();               // ex: starter, business, premium
            $table->enum('cible', ['b2c', 'b2b', 'producteur']); // pour qui
            $table->decimal('prix_mensuel', 8, 2);
            $table->decimal('prix_annuel', 8, 2)->nullable();
            $table->string('stripe_price_id_mensuel')->nullable();
            $table->string('stripe_price_id_annuel')->nullable();
            $table->json('fonctionnalites');                // liste des features incluses
            $table->unsignedInteger('nb_annonces_max')->default(0); // 0 = illimité
            $table->unsignedInteger('commission_pct')->default(12); // % commission Biocolis
            $table->boolean('livraison_incluse')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('ordre')->default(0);  // pour l'affichage
            $table->timestamps();
        });

        // Abonnements souscrits par les users
        Schema::create('abonnements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained();
            $table->enum('statut', ['actif', 'annule', 'expire', 'suspendu', 'essai'])
                  ->default('actif');
            $table->enum('periodicite', ['mensuel', 'annuel'])->default('mensuel');
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->string('stripe_customer_id')->nullable();
            $table->timestamp('debut_at');
            $table->timestamp('fin_at')->nullable();        // null = reconduit auto
            $table->timestamp('annule_at')->nullable();
            $table->timestamp('prochain_paiement_at')->nullable();
            $table->decimal('montant', 8, 2);               // montant payé
            $table->timestamps();

            $table->index(['user_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abonnements');
        Schema::dropIfExists('plans');
    }
};
