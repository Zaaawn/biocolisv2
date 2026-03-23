<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── CONVERSATIONS ─────────────────────────────────────────────────────
        // Une conversation = un fil entre acheteur + vendeur + annonce
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annonce_id')->constrained()->cascadeOnDelete();
            $table->foreignId('acheteur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vendeur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('commande_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamp('dernier_message_at')->nullable();
            $table->unsignedInteger('nb_messages')->default(0);

            // Non-lus par participant
            $table->unsignedInteger('non_lus_acheteur')->default(0);
            $table->unsignedInteger('non_lus_vendeur')->default(0);

            // Archivage (chaque participant peut archiver de son côté)
            $table->boolean('archive_acheteur')->default(false);
            $table->boolean('archive_vendeur')->default(false);

            $table->timestamps();

            $table->unique(['annonce_id', 'acheteur_id', 'vendeur_id']);
            $table->index(['acheteur_id', 'dernier_message_at']);
            $table->index(['vendeur_id', 'dernier_message_at']);
        });

        // ── MESSAGES ──────────────────────────────────────────────────────────
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();

            $table->text('contenu')->nullable();            // texte du message
            $table->json('images')->nullable();             // array de chemins images

            $table->enum('type', [
                'texte',
                'image',
                'systeme',          // ex: "Commande passée", "Livraison confirmée"
                'offre',            // proposition de prix (future feature)
            ])->default('texte');

            $table->boolean('is_read')->default(false);
            $table->timestamp('lu_at')->nullable();

            // Pour les messages système
            $table->string('systeme_type')->nullable();     // ex: 'commande_creee'
            $table->json('systeme_data')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};
