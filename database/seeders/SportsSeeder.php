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
                'id' => 1,
                'name' => 'Basketball',
            ],
            [
                'id' => 2,
                'name' => 'Volleyball',
            ],
            [
                'id' => 3,
                'name' => 'Floorball',
            ],
            [
                'id' => 4,
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

