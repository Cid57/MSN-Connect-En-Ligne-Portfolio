<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un admin
        \App\Models\User::create([
            'name' => 'Admin',
            'first_name' => 'Cindy',
            'last_name' => 'Singer',
            'email' => 'admin@msnconnect.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'is_active' => true,
            'status_id' => 1, // Disponible
            'status_message' => 'Administrateur du système',
            'last_seen_at' => now(),
        ]);

        // Créer des utilisateurs de test
        \App\Models\User::create([
            'name' => 'Jean Dupont',
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'is_active' => true,
            'status_id' => 1,
            'status_message' => 'En ligne et disponible',
            'last_seen_at' => now(),
        ]);

        \App\Models\User::create([
            'name' => 'Marie Martin',
            'first_name' => 'Marie',
            'last_name' => 'Martin',
            'email' => 'marie.martin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'is_active' => true,
            'status_id' => 2, // Absent
            'status_message' => 'De retour dans 1h',
            'last_seen_at' => now()->subHours(2),
        ]);

        \App\Models\User::create([
            'name' => 'Pierre Bernard',
            'first_name' => 'Pierre',
            'last_name' => 'Bernard',
            'email' => 'pierre.bernard@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'is_active' => true,
            'status_id' => 3, // Occupé
            'status_message' => 'En réunion',
            'last_seen_at' => now()->subMinutes(10),
        ]);

        \App\Models\User::create([
            'name' => 'Sophie Dubois',
            'first_name' => 'Sophie',
            'last_name' => 'Dubois',
            'email' => 'sophie.dubois@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'is_active' => true,
            'status_id' => 1,
            'status_message' => 'Disponible pour discuter !',
            'last_seen_at' => now(),
        ]);
    }
}
