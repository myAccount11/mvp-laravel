<?php

namespace Database\Seeders;

use App\Models\V5\Season;
use Illuminate\Database\Seeder;

class SeasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasons = [
            [
                'name' => '2024/2025',
            ],
        ];

        foreach ($seasons as $season) {
            Season::updateOrCreate(
                $season
            );
        }
    }
}

