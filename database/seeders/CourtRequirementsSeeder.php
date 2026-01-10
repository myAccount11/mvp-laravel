<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\V5\CourtRequirement;
use Illuminate\Support\Facades\DB;

class CourtRequirementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Court requirements that are used by the system
        // These IDs (1, 2, 3, 4) are hardcoded in the CourtService
        $requirements = [
            ['id' => 1, 'name' => 'Liga/Div', 'season_sport_id' => 0],
            ['id' => 2, 'name' => 'Sen ungdom', 'season_sport_id' => 0],
            ['id' => 3, 'name' => 'Øvrige', 'season_sport_id' => 0],
            ['id' => 4, 'name' => 'Mini', 'season_sport_id' => 0],
        ];

        foreach ($requirements as $requirement) {
            // Use DB::table to insert with specific ID
            DB::table('court_requirements')->updateOrInsert(
                ['id' => $requirement['id']],
                [
                    'name' => $requirement['name'],
                    'season_sport_id' => $requirement['season_sport_id'],
                ]
            );
        }
    }
}
