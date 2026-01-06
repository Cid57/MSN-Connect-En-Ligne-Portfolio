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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id') // Channel auquel appartient le message
                  ->constrained('channels')
                  ->onDelete('cascade');
            $table->foreignId('user_id') // Auteur du message
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->text('content'); // Contenu du message
            $table->string('attachment')->nullable(); // Fichier attaché (image, document)
            $table->string('attachment_type', 50)->nullable(); // Type: image, document, video
            $table->boolean('is_read')->default(false); // Message lu
            $table->timestamp('read_at')->nullable(); // Date de lecture
            $table->softDeletes(); // Suppression douce des messages
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index('channel_id');
            $table->index('user_id');
            $table->index('is_read');
            $table->index('created_at'); // Pour trier les messages par date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
