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
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Nom du groupe (null pour conversations privées)
            $table->text('description')->nullable(); // Description du groupe
            $table->boolean('is_group')->default(false); // true = groupe, false = conversation privée
            $table->boolean('is_active')->default(true); // Channel actif
            $table->foreignId('created_by')->nullable() // Créateur du channel
                  ->constrained('users')
                  ->onDelete('set null');
            $table->timestamp('last_message_at')->nullable(); // Dernière activité
            $table->softDeletes(); // Archivage des conversations
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index('is_group');
            $table->index('is_active');
            $table->index('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
