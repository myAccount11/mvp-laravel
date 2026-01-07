<?php

namespace Database\Seeders;

use App\Models\V5\User;
use App\Models\V5\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'mvpadmin@admin.com'],
            [
                'email' => 'mvpadmin@admin.com',
                'password' => Hash::make('mvpadmin123'),
            ]
        );

        UserRole::updateOrCreate(
            [
                'user_id' => $user->id,
                'role_id' => 1000,
            ],
            [
                'user_id' => $user->id,
                'role_id' => 1000,
                'user_role_approved_by_user_id' => 1,
                'club_id' => null,
                'team_id' => null,
                'season_sport_id' => null,
                'user_role_spec' => '',
            ]
        );
    }
}

