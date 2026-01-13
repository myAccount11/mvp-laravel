<?php

namespace Database\Seeders;

use App\Models\V5\Sport;
use Illuminate\Database\Seeder;

class SportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sports = [
            [
                'name' => 'Basketball',
            ],
            [
                'name' => 'Volleyball',
            ],
            [
                'name' => 'Floorball',
            ],
            [
                'name' => 'American Football',
            ],
        ];

        foreach ($sports as $sport) {
            Sport::updateOrCreate(
                ['id' => $sport['id']],
                $sport
            );
        }
    }
}

