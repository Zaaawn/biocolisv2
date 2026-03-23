<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cette migration ajoute la clé étrangère users → abonnements
// APRÈS que les deux tables soient créées (évite la dépendance circulaire)

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('abonnement_actif_id')
                  ->references('id')
                  ->on('abonnements')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['abonnement_actif_id']);
        });
    }
};
