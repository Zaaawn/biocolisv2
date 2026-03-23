<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── RATINGS (avis) ────────────────────────────────────────────────────
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annonce_id')->constrained()->cascadeOnDelete();

            // Qui note qui
            $table->foreignId('auteur_id')->constrained('users')->cascadeOnDelete();    // celui qui donne l'avis
            $table->foreignId('cible_id')->constrained('users')->cascadeOnDelete();     // celui qui reçoit l'avis

            $table->enum('sens', ['acheteur_note_vendeur', 'vendeur_note_acheteur']);

            $table->tinyInteger('note');                    // 1 à 5
            $table->text('commentaire')->nullable();
            $table->json('criteres')->nullable();           // ex: {"fraicheur":5,"emballage":4}

            // Modération
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_signale')->default(false);
            $table->text('reponse_vendeur')->nullable();    // vendeur peut répondre
            $table->timestamp('repondu_at')->nullable();

            $table->timestamps();

            // Un seul avis par commande + sens
            $table->unique(['commande_id', 'sens']);
            $table->index(['cible_id', 'is_visible']);
        });

        // ── LIKES / FAVORIS ───────────────────────────────────────────────────
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annonce_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'annonce_id']);
            $table->index('annonce_id');
        });

        // ── NOTIFICATIONS ─────────────────────────────────────────────────────
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_id', 'notifiable_type', 'read_at']);
        });

        // ── SIGNALEMENTS ──────────────────────────────────────────────────────
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auteur_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('cible');                        // annonce, user, message...
            $table->enum('motif', [
                'contenu_inapproprie',
                'arnaque',
                'faux_produit',
                'spam',
                'autre',
            ]);
            $table->text('description')->nullable();
            $table->enum('statut', ['en_attente', 'traite', 'rejete'])->default('en_attente');
            $table->text('note_admin')->nullable();
            $table->foreignId('traite_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('traite_at')->nullable();
            $table->timestamps();

            $table->index(['statut', 'created_at']);
        });

        // ── PANIER (persisté en BDD) ──────────────────────────────────────────
        Schema::create('panier_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annonce_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantite', 8, 2)->default(1);
            $table->enum('mode_livraison', [
                'main_propre',
                'point_relais',
                'domicile',
                'locker',
            ])->default('main_propre');
            $table->timestamps();

            $table->unique(['user_id', 'annonce_id']);
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('panier_lignes');
        Schema::dropIfExists('signalements');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('likes');
        Schema::dropIfExists('ratings');

    }
};
