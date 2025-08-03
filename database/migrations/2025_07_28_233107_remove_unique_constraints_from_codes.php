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
        // Supprimer les contraintes d'unicité sur les codes pour permettre les doublons
        Schema::table('piliers', function (Blueprint $table) {
            try {
                $table->dropUnique(['code']);
            } catch (Exception $e) {
                // La contrainte n'existe peut-être pas
            }
        });

        Schema::table('objectif_specifiques', function (Blueprint $table) {
            try {
                $table->dropUnique(['code']);
            } catch (Exception $e) {
                // La contrainte n'existe peut-être pas
            }
        });

        Schema::table('actions', function (Blueprint $table) {
            try {
                $table->dropUnique(['code']);
            } catch (Exception $e) {
                // La contrainte n'existe peut-être pas
            }
        });

        Schema::table('sous_actions', function (Blueprint $table) {
            try {
                $table->dropUnique(['code']);
            } catch (Exception $e) {
                // La contrainte n'existe peut-être pas
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre les contraintes d'unicité
        Schema::table('piliers', function (Blueprint $table) {
            $table->unique('code');
        });

        Schema::table('objectif_specifiques', function (Blueprint $table) {
            $table->unique('code');
        });

        Schema::table('actions', function (Blueprint $table) {
            $table->unique('code');
        });

        Schema::table('sous_actions', function (Blueprint $table) {
            $table->unique('code');
        });
    }
};
