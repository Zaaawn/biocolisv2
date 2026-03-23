<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Identité
            $table->string('prenom');
            $table->string('nom');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Rôle : particulier, professionnel, b2b, admin
            $table->enum('role', ['particulier', 'professionnel', 'b2b', 'admin'])->default('particulier');

            // Coordonnées
            $table->string('adresse');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('telephone')->nullable();
            $table->string('photo_profil')->nullable()->default('photos_profil/default.jpg');

            // Infos professionnelles (si role = professionnel ou b2b)
            $table->string('societe_nom')->nullable();
            $table->string('siret', 14)->nullable();
            $table->string('societe_adresse')->nullable();
            $table->string('tva_intracommunautaire')->nullable();

            // Stripe Connect (pour recevoir les paiements)
            $table->string('stripe_account_id')->nullable();
            $table->enum('stripe_account_status', ['non_cree', 'en_cours', 'actif', 'suspendu'])
                  ->default('non_cree');

            // Abonnement actif — clé étrangère ajoutée après création de la table abonnements
            $table->unsignedBigInteger('abonnement_actif_id')->nullable();

            // Statut du compte
            $table->boolean('is_active')->default(true);
            $table->boolean('is_banned')->default(false);
            $table->timestamp('banned_at')->nullable();
            $table->string('ban_reason')->nullable();

            // Stats rapides (dénormalisées pour les perfs)
            $table->unsignedInteger('nb_annonces')->default(0);
            $table->unsignedInteger('nb_ventes')->default(0);
            $table->unsignedInteger('nb_achats')->default(0);
            $table->decimal('note_moyenne', 3, 2)->default(0.00);
            $table->unsignedInteger('nb_avis')->default(0);

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Pour ne pas supprimer les données liées
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
