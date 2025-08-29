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
        Schema::table('validations', function (Blueprint $table) {
            // Type de validation (completion, progression_decrease, etc.)
            if (!Schema::hasColumn('validations', 'type')) {
                $table->string('type')->default('completion')->after('id');
            }
            
            // Valeurs avant/après modification
            if (!Schema::hasColumn('validations', 'current_value')) {
                $table->integer('current_value')->nullable()->after('type');
            }
            
            if (!Schema::hasColumn('validations', 'requested_value')) {
                $table->integer('requested_value')->nullable()->after('current_value');
            }
            
            // Raison de la demande et raison du rejet
            if (!Schema::hasColumn('validations', 'reason')) {
                $table->text('reason')->nullable()->after('requested_value');
            }
            
            if (!Schema::hasColumn('validations', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('reason');
            }
        });
        
        // Ajouter les index seulement s'ils n'existent pas
        $indexes = DB::select("SHOW INDEX FROM validations");
        $indexNames = array_column($indexes, 'Key_name');
        
        if (!in_array('validations_type_status_index', $indexNames)) {
            Schema::table('validations', function (Blueprint $table) {
                $table->index(['type', 'status']);
            });
        }
        
        if (!in_array('validations_element_type_element_id_index', $indexNames)) {
            Schema::table('validations', function (Blueprint $table) {
                $table->index(['element_type', 'element_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les index seulement s'ils existent
        $indexes = DB::select("SHOW INDEX FROM validations");
        $indexNames = array_column($indexes, 'Key_name');
        
        if (in_array('validations_type_status_index', $indexNames)) {
            Schema::table('validations', function (Blueprint $table) {
                $table->dropIndex(['type', 'status']);
            });
        }
        
        if (in_array('validations_element_type_element_id_index', $indexNames)) {
            Schema::table('validations', function (Blueprint $table) {
                $table->dropIndex(['element_type', 'element_id']);
            });
        }
        
        Schema::table('validations', function (Blueprint $table) {
            // Supprimer seulement les colonnes qui ont été ajoutées
            $columnsToDrop = [];
            
            if (Schema::hasColumn('validations', 'type')) {
                $columnsToDrop[] = 'type';
            }
            if (Schema::hasColumn('validations', 'current_value')) {
                $columnsToDrop[] = 'current_value';
            }
            if (Schema::hasColumn('validations', 'requested_value')) {
                $columnsToDrop[] = 'requested_value';
            }
            if (Schema::hasColumn('validations', 'reason')) {
                $columnsToDrop[] = 'reason';
            }
            if (Schema::hasColumn('validations', 'rejection_reason')) {
                $columnsToDrop[] = 'rejection_reason';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
