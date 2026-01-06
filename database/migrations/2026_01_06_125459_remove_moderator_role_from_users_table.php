<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convertir les modérateurs en utilisateurs
        DB::table('users')->where('role', 'moderator')->update(['role' => 'user']);

        // Modifier la colonne enum pour retirer 'moderator'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin') DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre 'moderator' dans l'enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'moderator', 'admin') DEFAULT 'user'");
    }
};
