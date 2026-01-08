<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\V5\Region;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            ['name' => 'DK National', 'season_sport_id' => 1],
            ['name' => 'DK Øst', 'season_sport_id' => 1],
            ['name' => 'DK Vest', 'season_sport_id' => 1],
            ['name' => 'DK National', 'season_sport_id' => 2],
            ['name' => 'DK Øst', 'season_sport_id' => 2],
            ['name' => 'DK Vest', 'season_sport_id' => 2],
            ['name' => 'National', 'season_sport_id' => 5],
            ['name' => 'VD', 'season_sport_id' => 6],
            ['name' => 'FVBK', 'season_sport_id' => 6],
            ['name' => 'MJVB', 'season_sport_id' => 6],
            ['name' => 'SVBK', 'season_sport_id' => 6],
            ['name' => 'NVBK', 'season_sport_id' => 6],
            ['name' => 'SyVBK', 'season_sport_id' => 6],
            ['name' => 'National', 'season_sport_id' => 7],
            ['name' => 'National', 'season_sport_id' => 11],
            ['name' => 'VD', 'season_sport_id' => 12],
            ['name' => 'FVBK', 'season_sport_id' => 12],
            ['name' => 'MJVB', 'season_sport_id' => 12],
            ['name' => 'SVBK', 'season_sport_id' => 12],
            ['name' => 'NVBK', 'season_sport_id' => 12],
            ['name' => 'SyVBK', 'season_sport_id' => 12],
            ['name' => 'National', 'season_sport_id' => 13],
            ['name' => 'Øst', 'season_sport_id' => 11],
            ['name' => 'Vest', 'season_sport_id' => 11],
        ];

        foreach ($regions as $region) {
            Region::firstOrCreate(
                ['name' => $region['name'], 'season_sport_id' => $region['season_sport_id']],
                $region
            );
        }
    }
}
