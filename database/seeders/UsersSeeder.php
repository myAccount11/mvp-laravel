<?php

namespace Database\Seeders;

use App\Models\V5\Role;
use App\Models\V5\User;
use App\Models\V5\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emails = [
            'club_manager@test.com',
            'ref_responsible@test.com',
            'officials_responsible@test.com',
            'other_club_role@test.com',
            'head_coach@test.com',
            'assistant_coach@test.com',
            'team_manager@test.com',
            'player@test.com',
            'parent@test.com',
            'volunteer@test.com',
            'referee@test.com',
            'club_chairman@test.com',
            'club_finance@test.com',
            'association_admin@test.com',
            'association_ref_admin@test.com',
            'association_coach_admin@test.com',
        ];

        $password = Hash::make('mvpadmin123');

        $users = [];
        foreach ($emails as $email) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'email' => $email,
                    'password' => $password,
                ]
            );
            $users[] = $user;
        }

        $roles = Role::all();
        $fields = [
            'user_role_approved_by_user_id' => 1,
            'club_id' => null,
            'team_id' => null,
            'season_sport_id' => null,
            'user_role_spec' => '',
        ];

        foreach ($users as $index => $user) {
            if (isset($roles[$index])) {
                UserRole::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'role_id' => $roles[$index]->id,
                    ],
                    array_merge(
                        [
                            'user_id' => $user->id,
                            'role_id' => $roles[$index]->id,
                        ],
                        $fields
                    )
                );
            }
        }
    }
}

