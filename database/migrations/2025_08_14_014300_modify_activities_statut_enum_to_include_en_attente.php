<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Étape 1: Changer temporairement la colonne en VARCHAR pour permettre la modification des données
        DB::statement("ALTER TABLE activities MODIFY COLUMN statut VARCHAR(20) DEFAULT 'a_faire'");
        
        // Étape 2: Mettre à jour les données 'a_faire' vers 'en_attente'
        DB::table('activities')
            ->where('statut', 'a_faire')
            ->update(['statut' => 'en_attente']);
        
        // Étape 3: Remettre la colonne en ENUM avec la nouvelle valeur par défaut
        DB::statement("ALTER TABLE activities MODIFY COLUMN statut ENUM('en_attente', 'en_cours', 'termine', 'bloque') DEFAULT 'en_attente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Étape 1: Changer temporairement la colonne en VARCHAR
        DB::statement("ALTER TABLE activities MODIFY COLUMN statut VARCHAR(20) DEFAULT 'en_attente'");
        
        // Étape 2: Remettre les données 'en_attente' vers 'a_faire'
        DB::table('activities')
            ->where('statut', 'en_attente')
            ->update(['statut' => 'a_faire']);
        
        // Étape 3: Remettre la colonne en ENUM avec l'ancienne valeur par défaut
        DB::statement("ALTER TABLE activities MODIFY COLUMN statut ENUM('a_faire', 'en_cours', 'termine', 'bloque') DEFAULT 'a_faire'");
    }
};
