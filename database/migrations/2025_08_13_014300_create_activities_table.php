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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sous_action_id')->constrained('sous_actions')->onDelete('cascade');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->datetime('date_debut');
            $table->datetime('date_fin');
            $table->integer('duree_estimee')->default(1); // en heures
            $table->enum('priorite', ['basse', 'moyenne', 'haute', 'critique'])->default('moyenne');
            $table->enum('statut', ['en_attente', 'en_cours', 'termine', 'bloque'])->default('en_attente');
            $table->decimal('taux_avancement', 5, 2)->default(0.00); // 0.00 à 100.00
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['sous_action_id', 'statut']);
            $table->index(['owner_id', 'statut']);
            $table->index(['date_debut', 'date_fin']);
            $table->index(['priorite', 'statut']);
            $table->index('taux_avancement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
