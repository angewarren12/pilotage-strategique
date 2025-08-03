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
        Schema::create('piliers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // P1, P2, etc.
            $table->string('libelle'); // Libellé du pilier
            $table->decimal('taux_avancement', 5, 2)->default(0); // Taux calculé automatiquement
            $table->unsignedBigInteger('owner_id')->nullable(); // Owner du pilier
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piliers');
    }
};
