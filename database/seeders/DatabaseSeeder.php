<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in order to respect dependencies
        $this->call([
            RolesSeeder::class,           // Must be first (needed by SuperUserSeeder and UsersSeeder)
            SportsSeeder::class,          // Needed by SeasonSportSeeder
            OrganizersSeeder::class,      // Needed by SeasonsSeeder
            SeasonsSeeder::class,         // Needed by SeasonSportSeeder
            SeasonSportSeeder::class,     // Depends on SeasonsSeeder and SportsSeeder
            SuperUserSeeder::class,       // Depends on RolesSeeder
            TournamentTypesSeeder::class, // Depends on SeasonSportSeeder (needs season_sport_id = 2)
            RegionsSeeder::class,         // Depends on SeasonSportSeeder (needs season_sport_id)
            CourtRequirementsSeeder::class, // Court requirements (IDs 1-4 used by CourtService)
            TournamentProgramsSeeder::class,
            TournamentProgramItemsSeeder::class,
        ]);
    }
}
