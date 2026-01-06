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
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // Ex: 'Disponible', 'Absent', 'Occupé', 'Ne pas déranger'
            $table->string('color', 20)->nullable(); // Couleur associée au statut
            $table->string('icon', 50)->nullable(); // Icône du statut
            $table->boolean('is_available')->default(true); // Si l'utilisateur est considéré disponible
            $table->integer('sort_order')->default(0); // Ordre d'affichage
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
