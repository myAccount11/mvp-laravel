<?php

namespace Database\Seeders;

use App\Models\V5\SeasonSport;
use Illuminate\Database\Seeder;
use App\Models\V5\TournamentProgram;

class TournamentProgramsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds tournament_programs table with hardcoded data from member database
     */
    public function run(): void
    {
        $seasonSport = SeasonSport::first();
        $programs = [
            ['name' => '6x4', 'season_sport_id' => $seasonSport->id],
            ['name' => '11x2', 'season_sport_id' => $seasonSport->id],
            ['name' => '5x4', 'season_sport_id' => $seasonSport->id],
            ['name' => '3x4', 'season_sport_id' => $seasonSport->id],
            ['name' => '15x1', 'season_sport_id' => $seasonSport->id],
            ['name' => '14x1', 'season_sport_id' => $seasonSport->id],
        ];

        foreach ($programs as $program) {
            TournamentProgram::create(
                [
                    'name'            => $program['name'],
                    'season_sport_id' => $program['season_sport_id'],
                ]
            );
        }
    }
}
