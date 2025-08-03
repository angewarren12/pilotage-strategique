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
        Schema::create('objectif_strategiques', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // OS1, OS2, etc.
            $table->string('libelle'); // Libellé de l'objectif stratégique
            $table->decimal('taux_avancement', 5, 2)->default(0); // Taux calculé automatiquement
            $table->unsignedBigInteger('pilier_id'); // Référence au pilier parent
            $table->unsignedBigInteger('owner_id')->nullable(); // Owner de l'objectif stratégique
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            
            $table->foreign('pilier_id')->references('id')->on('piliers')->onDelete('cascade');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objectif_strategiques');
    }
};
