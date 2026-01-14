<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\V5\TournamentStructure;
use App\Models\V5\TournamentRegistrationType;

class TournamentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed tournament_registration_types
        $registrationTypes = [
            'Organizer places teams',
            'Open registration',
            'Registration by invitation',
        ];

        foreach ($registrationTypes as $name) {
            TournamentRegistrationType::firstOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        }

        // Seed tournament_structures
        $structures = [
            'Pools with standings',
            'Knockout',
            'Play-offs with placement matches',
        ];

        foreach ($structures as $name) {
            TournamentStructure::firstOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        }

    }
}
