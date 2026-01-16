<?php

namespace Database\Seeders;

use App\Models\V5\Club;
use App\Models\V5\Team;
use Illuminate\Database\Seeder;

class ClubsAndTeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 16 clubs with their respective teams using realistic football club names.
     */
    public function run(): void
    {
        $clubs = [
            [
                'name' => 'FC Barcelona',
                'short_name' => 'FCB',
                'city' => 'Barcelona',
                'country' => 'Spain',
                'team_name' => 'Barcelona',
            ],
            [
                'name' => 'Real Madrid CF',
                'short_name' => 'RMA',
                'city' => 'Madrid',
                'country' => 'Spain',
                'team_name' => 'Real Madrid',
            ],
            [
                'name' => 'Manchester United FC',
                'short_name' => 'MUN',
                'city' => 'Manchester',
                'country' => 'England',
                'team_name' => 'Man United',
            ],
            [
                'name' => 'Liverpool FC',
                'short_name' => 'LIV',
                'city' => 'Liverpool',
                'country' => 'England',
                'team_name' => 'Liverpool',
            ],
            [
                'name' => 'Bayern Munich',
                'short_name' => 'BAY',
                'city' => 'Munich',
                'country' => 'Germany',
                'team_name' => 'Bayern Munich',
            ],
            [
                'name' => 'Borussia Dortmund',
                'short_name' => 'BVB',
                'city' => 'Dortmund',
                'country' => 'Germany',
                'team_name' => 'Dortmund',
            ],
            [
                'name' => 'Juventus FC',
                'short_name' => 'JUV',
                'city' => 'Turin',
                'country' => 'Italy',
                'team_name' => 'Juventus',
            ],
            [
                'name' => 'AC Milan',
                'short_name' => 'ACM',
                'city' => 'Milan',
                'country' => 'Italy',
                'team_name' => 'AC Milan',
            ],
            [
                'name' => 'Paris Saint-Germain',
                'short_name' => 'PSG',
                'city' => 'Paris',
                'country' => 'France',
                'team_name' => 'PSG',
            ],
            [
                'name' => 'Chelsea FC',
                'short_name' => 'CHE',
                'city' => 'London',
                'country' => 'England',
                'team_name' => 'Chelsea',
            ],
            [
                'name' => 'Arsenal FC',
                'short_name' => 'ARS',
                'city' => 'London',
                'country' => 'England',
                'team_name' => 'Arsenal',
            ],
            [
                'name' => 'Manchester City FC',
                'short_name' => 'MCI',
                'city' => 'Manchester',
                'country' => 'England',
                'team_name' => 'Man City',
            ],
            [
                'name' => 'Atletico Madrid',
                'short_name' => 'ATM',
                'city' => 'Madrid',
                'country' => 'Spain',
                'team_name' => 'Atletico',
            ],
            [
                'name' => 'Inter Milan',
                'short_name' => 'INT',
                'city' => 'Milan',
                'country' => 'Italy',
                'team_name' => 'Inter Milan',
            ],
            [
                'name' => 'Ajax Amsterdam',
                'short_name' => 'AJX',
                'city' => 'Amsterdam',
                'country' => 'Netherlands',
                'team_name' => 'Ajax',
            ],
            [
                'name' => 'Benfica Lisbon',
                'short_name' => 'BEN',
                'city' => 'Lisbon',
                'country' => 'Portugal',
                'team_name' => 'Benfica',
            ],
        ];

        foreach ($clubs as $index => $clubData) {
            // Create the club
            $club = Club::create([
                'name' => $clubData['name'],
                'short_name' => $clubData['short_name'],
                'postal_city' => $clubData['city'],
                'country' => $clubData['country'],
                'address_line1' => $clubData['name'] . ' Stadium',
                'postal_code' => str_pad((string)rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                'phone_number1' => '+' . rand(1, 99) . ' ' . rand(100, 999) . ' ' . rand(1000000, 9999999),
                'email' => strtolower(str_replace(' ', '', $clubData['short_name'])) . '@' . strtolower(str_replace(' ', '', $clubData['name'])) . '.com',
                'web_address' => 'https://www.' . strtolower(str_replace(' ', '', $clubData['name'])) . '.com',
                'deleted' => false,
                'is_active' => true,
                'status' => 'active',
            ]);

            // Create the team for this club
            Team::create([
                'club_id' => $club->id,
                'local_name' => $clubData['name'] . ' First Team',
                'tournament_name' => $clubData['team_name'],
                'gender' => 'male',
                'age_group' => 'senior',
                'deleted' => false,
                'club_rank' => $index + 1,
            ]);
        }

        $this->command->info('Created 16 clubs with their teams successfully!');
    }
}

