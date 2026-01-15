<?php

namespace Database\Seeders;

use App\Models\V5\TournamentRegistrationType;
use Illuminate\Database\Seeder;
use App\Models\V5\TournamentStructure;

class TournamentStructuresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        $structures = TournamentStructure::getStructures();

        foreach ($structures as $structure) {
            TournamentStructure::firstOrCreate(
                $structure,
                $structure
            );
        }
    }
}
