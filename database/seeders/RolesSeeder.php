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
                'value' => Role::SUPER_ADMIN,
                'description' => 'Super Admin',
            ],
            [
                'value' => Role::CLUB_MANAGER,
                'description' => 'Club Manager',
            ],
            [
                'value' => Role::HEAD_COACH,
                'description' => 'Head Coach',
            ],
            [
                'value' => Role::ASSISTANT_COACH,
                'description' => 'Assistant Coach',
            ],
            [
                'value' => Role::TEAM_MANAGER,
                'description' => 'Team Manager',
            ],
            [
                'value' => Role::PLAYER,
                'description' => 'Player',
            ],
            [
                'value' => Role::ASSOCIATION_ADMIN,
                'description' => 'Association Admin',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate($role);
        }
    }
}

