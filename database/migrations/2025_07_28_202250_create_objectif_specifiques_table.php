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
        Schema::create('objectif_specifiques', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // PIL1, PIL2, etc.
            $table->string('libelle'); // Libellé de l'objectif spécifique
            $table->decimal('taux_avancement', 5, 2)->default(0); // Taux calculé automatiquement
            $table->unsignedBigInteger('objectif_strategique_id'); // Référence à l'objectif stratégique parent
            $table->unsignedBigInteger('owner_id')->nullable(); // Owner de l'objectif spécifique
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            
            $table->foreign('objectif_strategique_id')->references('id')->on('objectif_strategiques')->onDelete('cascade');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objectif_specifiques');
    }
};
