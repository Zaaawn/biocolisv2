<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── PAIEMENTS ─────────────────────────────────────────────────────────
        // Trace de TOUS les mouvements d'argent (achat, remboursement, commission...)
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // qui a payé/reçu

            $table->enum('type', [
                'achat',            // paiement acheteur
                'remboursement',    // remboursement acheteur
                'virement_vendeur', // transfert vers le vendeur
                'commission',       // commission prélevée par Biocolis
                'abonnement',       // paiement abonnement B2B
                'option_annonce',   // option payante sur annonce
            ]);

            $table->enum('statut', [
                'en_attente',
                'succes',
                'echec',
                'annule',
                'rembourse',
            ])->default('en_attente');

            $table->decimal('montant', 10, 2);
            $table->string('devise', 3)->default('EUR');

            // Références Stripe
            $table->string('stripe_id')->nullable()->unique(); // payment_intent, transfer, refund...
            $table->string('stripe_type')->nullable();          // 'payment_intent', 'transfer', 'refund'
            $table->json('stripe_metadata')->nullable();        // données brutes Stripe

            // Facturation
            $table->string('facture_numero')->nullable();
            $table->string('facture_url')->nullable();          // PDF Stripe ou custom

            $table->text('description')->nullable();
            $table->timestamp('traite_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'type', 'statut']);
            $table->index(['commande_id']);
        });

        // ── WEBHOOKS STRIPE ────────────────────────────────────────────────────
        // Log de tous les événements Stripe reçus (utile pour debug et rejeu)
        Schema::create('stripe_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_event_id')->unique();
            $table->string('type');                         // ex: payment_intent.succeeded
            $table->json('payload');                        // données brutes de l'événement
            $table->enum('statut', ['recu', 'traite', 'erreur', 'ignore'])->default('recu');
            $table->text('erreur')->nullable();
            $table->timestamp('traite_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_webhooks');
        Schema::dropIfExists('paiements');
    }
};
