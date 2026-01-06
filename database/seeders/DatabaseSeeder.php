<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ordre important : Statuses avant Users
        $this->call([
            StatusSeeder::class,
            UserSeeder::class,
            // ChannelSeeder::class, // À implémenter plus tard si besoin
        ]);
    }
}
