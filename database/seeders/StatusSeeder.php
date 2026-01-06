<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Disponible',
                'color' => 'green',
                'icon' => 'check-circle',
                'is_available' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Absent',
                'color' => 'yellow',
                'icon' => 'clock',
                'is_available' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Occupé',
                'color' => 'red',
                'icon' => 'minus-circle',
                'is_available' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Ne pas déranger',
                'color' => 'red',
                'icon' => 'x-circle',
                'is_available' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Invisible',
                'color' => 'gray',
                'icon' => 'eye-off',
                'is_available' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($statuses as $status) {
            \App\Models\Status::create($status);
        }
    }
}
