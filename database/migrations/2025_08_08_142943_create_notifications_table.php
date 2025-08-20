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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Destinataire de la notification
            $table->string('type'); // avancement_change, echeance_approche, delai_depasse, comment_new, validation_required
            $table->string('title'); // Titre de la notification
            $table->text('message'); // Message détaillé
            $table->json('data')->nullable(); // Données supplémentaires (ex: IDs des éléments concernés)
            $table->timestamp('read_at')->nullable(); // Date de lecture
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('channel')->default('database'); // database, email, push, sms
            $table->boolean('is_sent')->default(false); // Si la notification a été envoyée
            $table->timestamp('sent_at')->nullable(); // Date d'envoi
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['user_id', 'read_at']);
            $table->index(['type', 'created_at']);
            $table->index(['priority', 'created_at']);
            
            // Clé étrangère
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
