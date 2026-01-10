<?php

namespace Database\Seeders;

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
        $programs = [
            ['name' => '12x2', 'season_sport_id' => 17],
            ['name' => '10x2', 'season_sport_id' => 17],
            ['name' => '9x1', 'season_sport_id' => 17],
            ['name' => '8x1', 'season_sport_id' => 17],
            ['name' => '7x3', 'season_sport_id' => 17],
            ['name' => '5x2', 'season_sport_id' => 17],
            ['name' => '3x3', 'season_sport_id' => 17],
            ['name' => '10x1', 'season_sport_id' => 17],
            ['name' => '12x2', 'season_sport_id' => 17],
            ['name' => '4x3', 'season_sport_id' => 17],
            ['name' => '5x3', 'season_sport_id' => 17],
            ['name' => '6x3', 'season_sport_id' => 17],
            ['name' => '6x1', 'season_sport_id' => 17],
            ['name' => '7x1', 'season_sport_id' => 17],
            ['name' => '17x1', 'season_sport_id' => 17],
            ['name' => '11x1', 'season_sport_id' => 17],
            ['name' => '12x1', 'season_sport_id' => 17],
            ['name' => '7x2', 'season_sport_id' => 17],
            ['name' => '9x2', 'season_sport_id' => 17],
            ['name' => '8x2', 'season_sport_id' => 17],
            ['name' => '6x2', 'season_sport_id' => 17],
            ['name' => '4x2', 'season_sport_id' => 17],
            ['name' => '13x1', 'season_sport_id' => 17],
            ['name' => '6x4', 'season_sport_id' => 19],
            ['name' => '11x2', 'season_sport_id' => 19],
            ['name' => '5x4', 'season_sport_id' => 19],
            ['name' => '3x4', 'season_sport_id' => 19],
            ['name' => '15x1', 'season_sport_id' => 19],
            ['name' => '14x1', 'season_sport_id' => 19],
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
