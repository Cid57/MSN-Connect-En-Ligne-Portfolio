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
        Schema::create('channel_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id') // Référence au channel
                  ->constrained('channels')
                  ->onDelete('cascade');
            $table->foreignId('user_id') // Référence à l'utilisateur
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->boolean('is_admin')->default(false); // Admin du groupe
            $table->boolean('is_muted')->default(false); // Notifications désactivées
            $table->timestamp('joined_at')->useCurrent(); // Date d'ajout au channel
            $table->timestamp('last_read_at')->nullable(); // Dernière lecture
            $table->timestamps();

            // Contrainte unique : un user ne peut être qu'une fois dans un channel
            $table->unique(['channel_id', 'user_id']);

            // Index pour optimiser les requêtes
            $table->index('channel_id');
            $table->index('user_id');
            $table->index('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_user');
    }
};
