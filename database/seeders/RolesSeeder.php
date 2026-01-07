<?php

namespace Database\Seeders;

use App\Models\V5\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'value' => 'role_1',
                'description' => 'Club Manager',
            ],
            [
                'id' => 2,
                'value' => 'role_2',
                'description' => 'Ref Responsible',
            ],
            [
                'id' => 3,
                'value' => 'role_3',
                'description' => 'Officials Responsible',
            ],
            [
                'id' => 4,
                'value' => 'role_4',
                'description' => 'Other Club Role',
            ],
            [
                'id' => 5,
                'value' => 'role_5',
                'description' => 'Head Coach',
            ],
            [
                'id' => 6,
                'value' => 'role_6',
                'description' => 'Assistant Coach',
            ],
            [
                'id' => 7,
                'value' => 'role_7',
                'description' => 'Team Manager',
            ],
            [
                'id' => 8,
                'value' => 'role_8',
                'description' => 'Player',
            ],
            [
                'id' => 9,
                'value' => 'role_9',
                'description' => 'Parent',
            ],
            [
                'id' => 10,
                'value' => 'role_10',
                'description' => 'Volunteer',
            ],
            [
                'id' => 11,
                'value' => 'role_11',
                'description' => 'Referee',
            ],
            [
                'id' => 12,
                'value' => 'role_12',
                'description' => 'Club Chairman',
            ],
            [
                'id' => 13,
                'value' => 'role_13',
                'description' => 'Club Finance',
            ],
            [
                'id' => 100,
                'value' => 'role_100',
                'description' => 'Association Admin',
            ],
            [
                'id' => 101,
                'value' => 'role_101',
                'description' => 'Association Ref Admin',
            ],
            [
                'id' => 102,
                'value' => 'role_102',
                'description' => 'Association Coach Admin',
            ],
            [
                'id' => 1000,
                'value' => 'role_1000',
                'description' => 'Super Admin',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['id' => $role['id']],
                $role
            );
        }
    }
}

