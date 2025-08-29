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
        Schema::table('activities', function (Blueprint $table) {
            // Ajouter les colonnes manquantes
            $table->integer('duree_estimee')->default(1)->after('date_fin'); // en heures
            $table->json('tags')->nullable()->after('owner_id');
            
            // Modifier les types de date pour datetime
            $table->datetime('date_debut')->change();
            $table->datetime('date_fin')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées
            $table->dropColumn(['duree_estimee', 'tags']);
            
            // Remettre les types de date originaux
            $table->date('date_debut')->change();
            $table->date('date_fin')->change();
        });
    }
};
