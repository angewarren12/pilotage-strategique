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
        // Ajouter taux_avancement à la table piliers
        Schema::table('piliers', function (Blueprint $table) {
            $table->decimal('taux_avancement', 5, 2)->default(0.00)->after('libelle');
        });
        
        // Ajouter taux_avancement à la table objectif_strategiques
        Schema::table('objectif_strategiques', function (Blueprint $table) {
            $table->decimal('taux_avancement', 5, 2)->default(0.00)->after('libelle');
        });
        
        // Ajouter taux_avancement à la table objectif_specifiques
        Schema::table('objectif_specifiques', function (Blueprint $table) {
            $table->decimal('taux_avancement', 5, 2)->default(0.00)->after('libelle');
        });
        
        // Ajouter taux_avancement à la table actions
        Schema::table('actions', function (Blueprint $table) {
            $table->decimal('taux_avancement', 5, 2)->default(0.00)->after('libelle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
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
    }
};
