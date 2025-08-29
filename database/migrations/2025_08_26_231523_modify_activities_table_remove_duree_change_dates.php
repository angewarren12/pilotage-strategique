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
            // Supprimer le champ duree_estimee
            $table->dropColumn('duree_estimee');
            
            // Changer les types de date de datetime à date
            $table->date('date_debut')->change();
            $table->date('date_fin')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Remettre le champ duree_estimee
            $table->integer('duree_estimee')->default(1);
            
            // Remettre les types de date en datetime
            $table->datetime('date_debut')->change();
            $table->datetime('date_fin')->change();
        });
    }
};
