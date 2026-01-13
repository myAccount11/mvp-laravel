<?php

namespace Database\Seeders;

use App\Models\V5\Season;
use App\Models\V5\SeasonSport;
use App\Models\V5\Sport;
use Illuminate\Database\Seeder;

class SeasonSportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasons = Season::all();
        $sports = Sport::all();
        foreach ($seasons as $season) {
            foreach ($sports as $sport) {
                SeasonSport::updateOrCreate(
                    ['season_id' => $season->id, 'sport_id' => $sport->id],
                    ['season_id' => $season->id, 'sport_id' => $sport->id],
                );
            }
        }
    }
}

