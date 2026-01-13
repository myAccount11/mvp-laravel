<?php

namespace Database\Seeders;

use App\Models\V5\Role;
use App\Models\V5\User;
use App\Models\V5\UserRole;
use App\Models\V5\SeasonSport;
use App\Models\V5\UserSeasonSport;
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
        $superAdminRole = Role::query()->where('value', 'super_admin')->first();

        UserRole::updateOrCreate(
            [
                'user_id' => $user->id,
                'role_id' => $superAdminRole->id,
            ],
            [
                'user_id' => $user->id,
                'role_id' => $superAdminRole->id,
                'user_role_approved_by_user_id' => 1,
                'club_id' => null,
                'team_id' => null,
                'season_sport_id' => null,
                'user_role_spec' => '',
            ]
        );

        // Attach user to latest season sports (from the latest season)
        $latestSeason = \App\Models\V5\Season::orderBy('id', 'desc')->first();

        if ($latestSeason) {
            $latestSeasonSports = SeasonSport::where('season_id', $latestSeason->id)->get();

            foreach ($latestSeasonSports as $seasonSport) {
                UserSeasonSport::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'season_sport_id' => $seasonSport->id,
                    ],
                    [
                        'user_id' => $user->id,
                        'season_sport_id' => $seasonSport->id,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}

