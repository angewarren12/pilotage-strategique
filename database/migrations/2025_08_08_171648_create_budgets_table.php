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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            
            // Relations hiérarchiques (une seule peut être non-null)
            $table->unsignedBigInteger('pilier_id')->nullable();
            $table->unsignedBigInteger('objectif_strategique_id')->nullable();
            $table->unsignedBigInteger('objectif_specifique_id')->nullable();
            $table->unsignedBigInteger('action_id')->nullable();
            $table->unsignedBigInteger('sous_action_id')->nullable();
            
            // Informations budgétaires
            $table->decimal('montant_alloue', 15, 2)->default(0); // Montant alloué
            $table->decimal('montant_engage', 15, 2)->default(0); // Montant engagé
            $table->decimal('montant_realise', 15, 2)->default(0); // Montant réalisé
            $table->decimal('montant_restant', 15, 2)->default(0); // Montant restant
            
            // Période budgétaire
            $table->year('annee_budgetaire')->default(date('Y'));
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            
            // Statut et suivi
            $table->enum('statut', ['actif', 'inactif', 'archive'])->default('actif');
            $table->enum('type_budget', ['investissement', 'fonctionnement', 'personnel', 'autre'])->default('fonctionnement');
            $table->string('code_budget', 50)->nullable(); // Code budgétaire unique
            
            // Métadonnées
            $table->text('description')->nullable();
            $table->text('justification')->nullable();
            $table->string('source_financement', 100)->nullable(); // Source de financement
            
            // Responsable et validation
            $table->unsignedBigInteger('owner_id')->nullable(); // Responsable budgétaire
            $table->unsignedBigInteger('validated_by')->nullable(); // Validé par
            $table->timestamp('validated_at')->nullable();
            
            // Alertes et seuils
            $table->decimal('seuil_alerte', 5, 2)->default(80); // Seuil d'alerte en %
            $table->decimal('seuil_critique', 5, 2)->default(95); // Seuil critique en %
            $table->boolean('alertes_actives')->default(true);
            
            // Timestamps
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['pilier_id', 'annee_budgetaire']);
            $table->index(['objectif_strategique_id', 'annee_budgetaire']);
            $table->index(['objectif_specifique_id', 'annee_budgetaire']);
            $table->index(['action_id', 'annee_budgetaire']);
            $table->index(['sous_action_id', 'annee_budgetaire']);
            $table->index(['owner_id', 'statut']);
            $table->index(['annee_budgetaire', 'statut']);
            $table->index(['type_budget', 'statut']);
            
            // Contraintes de clés étrangères
            $table->foreign('pilier_id')->references('id')->on('piliers')->onDelete('cascade');
            $table->foreign('objectif_strategique_id')->references('id')->on('objectif_strategiques')->onDelete('cascade');
            $table->foreign('objectif_specifique_id')->references('id')->on('objectif_specifiques')->onDelete('cascade');
            $table->foreign('action_id')->references('id')->on('actions')->onDelete('cascade');
            $table->foreign('sous_action_id')->references('id')->on('sous_actions')->onDelete('cascade');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
            
            // Note: La contrainte de validation sera gérée au niveau de l'application
            // car la méthode check() n'est pas supportée dans cette version de Laravel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
