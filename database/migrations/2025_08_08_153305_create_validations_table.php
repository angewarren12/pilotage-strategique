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
        Schema::create('validations', function (Blueprint $table) {
            $table->id();
            $table->string('element_type'); // pilier, objectif_strategique, objectif_specifique, action, sous_action
            $table->unsignedBigInteger('element_id'); // ID de l'élément à valider
            $table->unsignedBigInteger('requested_by'); // Utilisateur qui demande la validation
            $table->unsignedBigInteger('validated_by')->nullable(); // Utilisateur qui valide
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('comments')->nullable(); // Commentaires de validation
            $table->text('rejection_reason')->nullable(); // Raison du rejet
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('validated_at')->nullable(); // Date de validation
            $table->timestamp('expires_at')->nullable(); // Date d'expiration de la demande
            $table->json('validation_data')->nullable(); // Données supplémentaires (anciennes valeurs, nouvelles valeurs, etc.)
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['element_type', 'element_id']);
            $table->index(['status', 'requested_at']);
            $table->index(['requested_by', 'status']);
            $table->index(['validated_by', 'status']);
            $table->index(['expires_at']);
            
            // Clés étrangères
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};
