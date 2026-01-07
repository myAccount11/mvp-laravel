<?php

namespace Database\Seeders;

use App\Models\V5\Organizer;
use Illuminate\Database\Seeder;

class OrganizersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizers = [
            [
                'id' => 1,
                'name' => 'DBBF',
            ],
            [
                'id' => 3,
                'name' => 'DVBF',
            ],
            [
                'id' => 4,
                'name' => 'DFF',
            ],
            [
                'id' => 5,
                'name' => 'LBA',
            ],
            [
                'id' => 6,
                'name' => 'OldboysDM',
            ],
            [
                'id' => 7,
                'name' => 'Hørsholm Cup',
            ],
            [
                'id' => 8,
                'name' => 'DAFF',
            ],
        ];

        foreach ($organizers as $organizer) {
            Organizer::updateOrCreate(
                ['id' => $organizer['id']],
                $organizer
            );
        }

        // Note: invoice_prefix field doesn't exist in the current migration
        // If needed, add it to the organizers table migration and model
    }
}

