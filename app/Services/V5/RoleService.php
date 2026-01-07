<?php

namespace App\Services\V5;

use App\Repositories\V5\RoleRepository;
use App\Repositories\V5\UserRoleRepository;
use App\Services\V5\UserService;
use App\Services\V5\TeamService;
use App\Services\V5\ClubService;
use App\Services\V5\PersonService;
use App\Services\V5\MessageService;
use Illuminate\Support\Facades\DB;
use App\Models\V5\User;

class RoleService
{
    protected $roleRepository;
    protected $userRoleRepository;
    protected $userService;
    protected $teamService;
    protected $clubService;
    protected $personService;
    protected $messageService;

    public function __construct(
        RoleRepository $roleRepository,
        UserRoleRepository $userRoleRepository,
        UserService $userService,
        TeamService $teamService,
        ClubService $clubService,
        PersonService $personService,
        MessageService $messageService
    ) {
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->userService = $userService;
        $this->teamService = $teamService;
        $this->clubService = $clubService;
        $this->personService = $personService;
        $this->messageService = $messageService;
    }

    public function createRole(array $data)
    {
        return $this->roleRepository->create($data);
    }

    public function getRoleByValue($value)
    {
        return $this->roleRepository->findByValue($value);
    }

    public function findAll(array $conditions = [])
    {
        $orderBy = $conditions['order_by'] ?? 'id';
        $orderDirection = $conditions['order_direction'] ?? 'asc';
        
        return $this->roleRepository->query()
            ->orderBy($orderBy, $orderDirection)
            ->get();
    }

    public function addUserRole(
        User $authUser,
        $userId,
        $roleId,
        $seasonSportId,
        $approvedBy = null,
        $teamId = null,
        $clubId = null
    ) {
        if ($roleId == 10) {
            $approvedBy = 1;
        }

        $userRoleId = 0;

        if ($teamId && !$clubId) {
            $team = $this->teamService->findOne(['id' => $teamId]);
            $clubId = $team->club_id;
        }

        if ($roleId >= 5 && $roleId <= 10 && $clubId > 0 && $teamId > 0) {
            $userRole = DB::table('user_roles')
                ->where('user_id', $userId)
                ->where('role_id', $roleId)
                ->where(function($q) use ($teamId) {
                    $q->whereIn('team_id', [0, $teamId])->orWhereNull('team_id');
                })
                ->where('club_id', $clubId)
                ->where('user_role_approved_by_user_id', '>=', 0)
                ->first();

            if ($userRole) {
                $userRoleId = $userRole->id;
            } else {
                $userRole = DB::table('user_roles')
                    ->where('user_id', $userId)
                    ->where('role_id', $roleId)
                    ->where(function($q) use ($teamId) {
                        $q->whereIn('team_id', [0, $teamId])->orWhereNull('team_id');
                    })
                    ->where('club_id', $clubId)
                    ->where('user_role_approved_by_user_id', '<', 0)
                    ->first();
                $userRoleId = $userRole ? $userRole->id : null;
            }
        }

        if ($userRoleId) {
            DB::table('user_roles')
                ->where('id', $userRoleId)
                ->update([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'club_id' => $clubId ?: null,
                    'team_id' => $teamId ?: null,
                    'user_role_approved_by_user_id' => $approvedBy,
                    'season_sport_id' => $seasonSportId,
                ]);
        } else {
            $existing = DB::table('user_roles')
                ->where('user_id', $userId)
                ->where('role_id', $roleId)
                ->where('team_id', $teamId ?: null)
                ->where('club_id', $clubId ?: null)
                ->where('season_sport_id', $seasonSportId)
                ->first();

            if ($existing) {
                DB::table('user_roles')
                    ->where('id', $existing->id)
                    ->update(['user_role_approved_by_user_id' => $approvedBy]);
            } else {
                DB::table('user_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'club_id' => $clubId ?: null,
                    'team_id' => $teamId ?: null,
                    'user_role_approved_by_user_id' => $approvedBy,
                    'season_sport_id' => $seasonSportId,
                ]);
            }
        }

        $role = $this->roleRepository->find($roleId);
        $user = $this->userService->findOne(['id' => $authUser->id]);

        $assignTarget = 'the club';
        if ($teamId) {
            $team = $this->teamService->findOne(['id' => $teamId]);
            $assignTarget = $team->tournament_name;
        } elseif ($clubId) {
            $club = $this->clubService->findOne(['id' => $clubId]);
            $assignTarget = $club->name;
        }

        if ($approvedBy) {
            $this->messageService->create([
                'type_id' => 1,
                'to_id' => $userId,
                'user_id' => $authUser->id === $userId ? 1473 : $authUser->id,
                'html' => "You have been assigned the role <strong>{$role->description}</strong> for <strong>{$assignTarget}</strong> by <strong>{$user->name}</strong>. <br /><br />Download the MVP App in the App Store or Google Play to use your new role :-)",
            ]);
        } elseif ($teamId) {
            $coaches = DB::table('user_roles')
                ->where('user_role_approved_by_user_id', '>=', 0)
                ->whereIn('role_id', [5, 6, 7])
                ->where('team_id', $teamId)
                ->get();

            foreach ($coaches as $coach) {
                $this->messageService->create([
                    'type_id' => 1,
                    'to_id' => $coach->user_id,
                    'user_id' => 1473,
                    'html' => "{$user->name} has applied for role <strong> {$role->description} </strong> for <strong>{$assignTarget}</strong>. <br /><br />Press the APPROVE ROLES menu in MVP App or MVP Web to reject or approve",
                ]);
            }
        }

        $this->personService->syncUserWithPerson($userId, $seasonSportId);
    }

    public function detachUserRole(User $authUser, $userId, array $userRoleIds)
    {
        $userRoles = DB::table('user_roles')
            ->whereIn('id', $userRoleIds)
            ->get();

        foreach ($userRoles as $userRole) {
            $target = '';
            if ($userRole->team_id) {
                $team = $this->teamService->findOne(['id' => $userRole->team_id]);
                $target = $team->local_name;
            } elseif ($userRole->club_id) {
                $club = $this->clubService->findOne(['id' => $userRole->club_id]);
                $target = $club->name;
            }

            if ($userRole->role_id >= 5 && $userRole->role_id <= 9 && !$userRole->team_id) {
                $check = DB::table('user_roles')
                    ->where('id', '!=', $userRole->id)
                    ->where('user_id', $userRole->user_id)
                    ->where('role_id', $userRole->role_id)
                    ->where('season_sport_id', $userRole->season_sport_id)
                    ->where('user_role_approved_by_user_id', '>', 0)
                    ->first();

                if ($check) {
                    $target = '';
                }
            }

            if ($target) {
                $role = $this->roleRepository->find($userRole->role_id);
                $this->messageService->create([
                    'type_id' => 1,
                    'to_id' => $userRole->user_id,
                    'user_id' => $authUser->id === $userId ? 1473 : $authUser->id,
                    'html' => "Your role <strong>{$role->description}</strong> for <strong>{$target}</strong> is DELETED by <strong>{$authUser->name}</strong>",
                ]);
            }
        }

        DB::table('user_roles')
            ->whereIn('id', $userRoleIds)
            ->update(['user_role_approved_by_user_id' => -1]);

        if (count($userRoles) > 0) {
            $this->personService->syncUserWithPerson($userId, $userRoles[0]->season_sport_id);
        }

        return true;
    }

    public function approveUserRole(User $authUser, $userId, array $userRoleIds)
    {
        $userRoles = DB::table('user_roles')
            ->whereIn('id', $userRoleIds)
            ->get();

        foreach ($userRoles as $userRole) {
            $target = '';
            if ($userRole->team_id) {
                $team = $this->teamService->findOne(['id' => $userRole->team_id]);
                $target = $team->local_name;
            } elseif ($userRole->club_id) {
                $club = $this->clubService->findOne(['id' => $userRole->club_id]);
                $target = $club->name;
            }

            if ($target) {
                $role = $this->roleRepository->find($userRole->role_id);
                $this->messageService->create([
                    'type_id' => 1,
                    'to_id' => $userRole->user_id,
                    'user_id' => $authUser->id === $userId ? 1473 : $authUser->id,
                    'html' => "Your role <strong>{$role->description}</strong> for <strong>{$target}</strong> is APPROVED by <strong>{$authUser->name}</strong>. <br /><br />Download the MVP App in the App Store or Google Play to use your new role :-)",
                ]);
            }
        }

        DB::table('user_roles')
            ->whereIn('id', $userRoleIds)
            ->update(['user_role_approved_by_user_id' => 1]);

        if (count($userRoles) > 0) {
            $this->personService->syncUserWithPerson($userId, $userRoles[0]->season_sport_id);
        }

        return true;
    }
}

