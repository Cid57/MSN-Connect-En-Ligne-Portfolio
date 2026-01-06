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
        Schema::table('users', function (Blueprint $table) {
            // Champs de profil utilisateur
            $table->string('first_name', 100)->nullable()->after('name');
            $table->string('last_name', 100)->nullable()->after('first_name');
            $table->string('avatar')->nullable()->after('email'); // Chemin vers l'avatar
            $table->boolean('is_admin')->default(false)->after('password'); // Rôle admin
            $table->boolean('is_active')->default(true)->after('is_admin'); // Compte actif

            // Statut utilisateur
            $table->foreignId('status_id')->nullable()->after('is_active')
                  ->constrained('statuses')
                  ->onDelete('set null');

            // Message personnalisé du statut
            $table->string('status_message')->nullable()->after('status_id');

            // Dates importantes
            $table->timestamp('last_seen_at')->nullable()->after('remember_token');
            $table->softDeletes(); // Pour la suppression douce

            // Index pour optimiser les requêtes
            $table->index('is_active');
            $table->index('is_admin');
            $table->index('last_seen_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_admin']);
            $table->dropIndex(['last_seen_at']);
            $table->dropColumn([
                'first_name',
                'last_name',
                'avatar',
                'is_admin',
                'is_active',
                'status_id',
                'status_message',
                'last_seen_at',
                'deleted_at',
            ]);
        });
    }
};
