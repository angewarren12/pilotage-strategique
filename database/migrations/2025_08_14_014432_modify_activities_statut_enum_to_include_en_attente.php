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
        // Modifier la colonne statut pour accepter 'en_attente' au lieu de 'a_faire'
        DB::statement("ALTER TABLE activities MODIFY COLUMN statut ENUM('en_attente', 'en_cours', 'termine', 'bloque') DEFAULT 'en_attente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre la colonne statut à l'état précédent
        DB::statement("ALTER TABLE activities MODIFY COLUMN statut ENUM('a_faire', 'en_cours', 'termine', 'bloque') DEFAULT 'a_faire'");
    }
};
