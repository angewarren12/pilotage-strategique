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
        Schema::create('sous_actions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // SA1, SA2, etc.
            $table->string('libelle'); // Libellé de la sous-action
            $table->decimal('taux_avancement', 5, 2)->default(0); // Saisie manuelle en %
            $table->unsignedBigInteger('action_id'); // Référence à l'action parent
            $table->unsignedBigInteger('owner_id')->nullable(); // Owner de la sous-action
            $table->text('description')->nullable();
            $table->date('date_echeance')->nullable(); // Date d'échéance
            $table->date('date_realisation')->nullable(); // Date de réalisation
            $table->integer('ecart_jours')->nullable(); // Écart en jours (calculé)
            $table->enum('statut', ['en_cours', 'termine', 'en_retard'])->default('en_cours');
            $table->boolean('actif')->default(true);
            $table->timestamps();
            
            $table->foreign('action_id')->references('id')->on('actions')->onDelete('cascade');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sous_actions');
    }
};
