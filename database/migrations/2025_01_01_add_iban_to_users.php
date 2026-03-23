<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // IBAN pour recevoir les virements Biocolis
            $table->string('iban', 34)->nullable()->after('adresse');
            $table->string('bic', 11)->nullable()->after('iban');
            $table->string('titulaire_compte', 255)->nullable()->after('bic');

            // Solde disponible (argent en attente de virement)
            $table->decimal('solde_disponible', 10, 2)->default(0)->after('titulaire_compte');
            $table->decimal('solde_en_attente', 10, 2)->default(0)->after('solde_disponible');
            $table->decimal('total_recu', 10, 2)->default(0)->after('solde_en_attente');

            // Date du dernier virement reçu
            $table->timestamp('dernier_virement_at')->nullable()->after('total_recu');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'iban', 'bic', 'titulaire_compte',
                'solde_disponible', 'solde_en_attente', 'total_recu',
                'dernier_virement_at',
            ]);
        });
    }
};
