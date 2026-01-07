<?php

namespace Database\Seeders;

use App\Models\V5\SeasonSport;
use Illuminate\Database\Seeder;

class SeasonSportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasonSports = [
            [
                'id' => 1,
                'season_id' => 1,
                'sport_id' => 1,
            ],
            [
                'id' => 2,
                'season_id' => 2,
                'sport_id' => 1,
            ],
            [
                'id' => 3,
                'season_id' => 3,
                'sport_id' => 2,
            ],
            [
                'id' => 4,
                'season_id' => 4,
                'sport_id' => 3,
            ],
            [
                'id' => 5,
                'season_id' => 5,
                'sport_id' => 1,
            ],
            [
                'id' => 6,
                'season_id' => 6,
                'sport_id' => 2,
            ],
            [
                'id' => 7,
                'season_id' => 7,
                'sport_id' => 3,
            ],
            [
                'id' => 8,
                'season_id' => 8,
                'sport_id' => 1,
            ],
            [
                'id' => 9,
                'season_id' => 9,
                'sport_id' => 1,
            ],
            [
                'id' => 10,
                'season_id' => 10,
                'sport_id' => 1,
            ],
            [
                'id' => 11,
                'season_id' => 11,
                'sport_id' => 1,
            ],
            [
                'id' => 12,
                'season_id' => 12,
                'sport_id' => 2,
            ],
            [
                'id' => 13,
                'season_id' => 13,
                'sport_id' => 3,
            ],
            [
                'id' => 14,
                'season_id' => 14,
                'sport_id' => 1,
            ],
            [
                'id' => 15,
                'season_id' => 15,
                'sport_id' => 1,
            ],
            [
                'id' => 16,
                'season_id' => 16,
                'sport_id' => 4,
            ],
            [
                'id' => 17,
                'season_id' => 17,
                'sport_id' => 1,
            ],
            [
                'id' => 18,
                'season_id' => 18,
                'sport_id' => 3,
            ],
            [
                'id' => 19,
                'season_id' => 19,
                'sport_id' => 1,
            ],
            [
                'id' => 20,
                'season_id' => 20,
                'sport_id' => 3,
            ],
        ];

        foreach ($seasonSports as $seasonSport) {
            SeasonSport::updateOrCreate(
                ['id' => $seasonSport['id']],
                $seasonSport
            );
        }
    }
}

