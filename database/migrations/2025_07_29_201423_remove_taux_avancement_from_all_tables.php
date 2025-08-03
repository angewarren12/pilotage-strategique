<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Supprimer taux_avancement de la table piliers
        Schema::table('piliers', function (Blueprint $table) {
            $table->dropColumn('taux_avancement');
        });

        // Supprimer taux_avancement de la table objectif_strategiques
        Schema::table('objectif_strategiques', function (Blueprint $table) {
            $table->dropColumn('taux_avancement');
        });

        // Supprimer taux_avancement de la table objectif_specifiques
        Schema::table('objectif_specifiques', function (Blueprint $table) {
            $table->dropColumn('taux_avancement');
        });

        // Supprimer taux_avancement de la table actions
        Schema::table('actions', function (Blueprint $table) {
            $table->dropColumn('taux_avancement');
        });

        // Note: On garde taux_avancement dans sous_actions
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre taux_avancement dans la table piliers
        Schema::table('piliers', function (Blueprint $table) {
            $table->decimal('taux_avancement', 5, 2)->default(0);
        });

        // Remettre taux_avancement dans la table objectif_strategiques
        Schema::table('objectif_strategiques', function (Blueprint $table) {
            $table->decimal('taux_avancement', 5, 2)->default(0);
        });

        // Remettre taux_avancement dans la table objectif_specifiques
        Schema::table('objectif_specifiques', function (Blueprint $table) {
            $table->decimal('taux_avancement', 5, 2)->default(0);
        });

        // Remettre taux_avancement dans la table actions
        Schema::table('actions', function (Blueprint $table) {
            $table->decimal('taux_avancement', 5, 2)->default(0);
        });
    }
};
