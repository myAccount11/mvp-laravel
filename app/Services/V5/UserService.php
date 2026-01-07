<?php

namespace App\Services\V5;

use App\Repositories\V5\UserRepository;
use App\Services\MailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected $userRepository;
    protected $mailService;


    public function __construct(UserRepository $userRepository, MailService $mailService)
    {
        $this->userRepository = $userRepository;
        $this->mailService = $mailService;
    }

    public function createUser(array $data)
    {
        $lastLicense = $this->userRepository->getLastLicense();
        $data['password'] = Hash::make($data['password']);
        $data['email'] = strtolower($data['email']);
        $data['license'] = $lastLicense + 1;

        $user = $this->userRepository->create($data);

        // Send create password email
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
        $this->mailService->sendMailCreatePassword($user->email, $user->name, $token);

        return $user;
    }

    public function createUserByAdmin(array $data)
    {
        $lastLicense = $this->userRepository->getLastLicense();
        $data['license'] = $lastLicense + 1;
        $data['email'] = strtolower($data['email']);

        // Handle old user registration if in production
        if (config('app.env') === 'prod') {
            try {
                // Old user service logic would go here
                // For now, we'll skip it
            } catch (\Exception $e) {
                Log::error('Old user registration failed: ' . $e->getMessage());
            }
        }

        $user = $this->userRepository->create($data);

        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
        $this->mailService->sendMailCreatePassword($user->email, $user->name, $token);

        return $user;
    }

    public function verifyUser($id)
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new \Exception('User not found');
        }

        $user->is_verified = true;
        $user->save();

        return $user;
    }

    public function getUsers($orderBy = 'id', $orderDirection = 'asc')
    {
        return $this->userRepository->query()
            ->with('roles')
            ->select('id', 'email', 'name', 'disable_emails', 'license', 'gender',
                     'birth_year', 'birth_month', 'birth_day', 'nationality',
                     'address_line1', 'address_line2', 'postal_code', 'city',
                     'country', 'phone_numbers', 'debtor_number', 'latlng', 'is_verified')
            ->orderBy($orderBy, $orderDirection)
            ->get();
    }

    public function findAndCountAll(array $conditions = [])
    {
        return $this->userRepository->findAndCountAll($conditions);
    }

    public function findOne(array $conditions)
    {
        return $this->userRepository->findOneBy($conditions);
    }

    public function findTeamUser($id, array $query = [])
    {
        $user = $this->userRepository->findWithRelations($id, [
            'userRoles' => function($q) use ($query) {
                if (isset($query['season_sport_id'])) {
                    $q->where('season_sport_id', $query['season_sport_id'])
                      ->where('user_role_approved_by_user_id', '>=', 0);
                }
            },
            'roles' => function($q) use ($query) {
                if (isset($query['season_sport_id'])) {
                    $q->wherePivot('season_sport_id', $query['season_sport_id']);
                }
            },
            'teams' => function($q) use ($query) {
                if (isset($query['season_sport_id'])) {
                    $q->wherePivot('season_sport_id', $query['season_sport_id']);
                }
            },
            'clubs' => function($q) use ($query) {
                if (isset($query['season_sport_id'])) {
                    $q->wherePivot('season_sport_id', $query['season_sport_id']);
                }
            }
        ]);

        if (!$user) {
            return null;
        }

        // Check for player data
        if (isset($query['season_sport_id'])) {
            $person = DB::table('person')
                ->where('season_sport_id', $query['season_sport_id'])
                ->where('user_id', $user->id)
                ->first();

            if ($person) {
                $player = DB::table('player')
                    ->where('person_id', $person->id)
                    ->first();

                if ($player) {
                    $user->player = $player;
                }
            }
        }

        return $user;
    }

    public function deleteUser($id)
    {
        return $this->userRepository->delete($id);
    }

    public function updateUser(array $condition, array $data)
    {
        // Check email uniqueness if email is being updated
        if (isset($data['email'])) {
            $existingUser = $this->userRepository->query()
                ->where('email', $data['email'])
                ->where('id', '!=', $condition['id'] ?? null)
                ->first();

            if ($existingUser) {
                throw new \Exception('Email must be unique.');
            }
        }

        return $this->userRepository->updateByCondition($condition, $data);
    }

    public function clubManagerNames($roleId, $clubId)
    {
        $userIds = DB::table('user_roles')
            ->where('role_id', $roleId)
            ->where('club_id', $clubId)
            ->pluck('user_id')
            ->toArray();

        $count = count($userIds);
        $rows = $this->userRepository->query()
            ->whereIn('id', $userIds)
            ->get();

        return ['rows' => $rows, 'count' => $count];
    }

    public function findAndCountAllClubOrTeamUsers(array $conditions = [])
    {
        // Implementation similar to NestJS findAndCountAllClubOrTeamUsers
        $query = $this->userRepository->query();

        if (isset($conditions['search_term'])) {
            $searchTerm = $conditions['search_term'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'ilike', "%{$searchTerm}%")
                  ->orWhere('email', 'ilike', "%{$searchTerm}%");
            });
        }

        $clubId = $conditions['club_id'] ?? null;
        $teamId = $conditions['team_id'] ?? null;
        $seasonSportId = $conditions['season_sport_id'] ?? null;
        $roles = $conditions['roles'] ?? null;

        $userIds = [];
        if ($clubId) {
            $roleIds = $roles ? (is_array($roles) ? $roles : [$roles]) : null;
            $queryBuilder = \DB::table('user_roles')
                ->where('club_id', $clubId)
                ->where('season_sport_id', $seasonSportId);

            if ($roleIds) {
                $queryBuilder->whereIn('role_id', array_map('intval', $roleIds));
            }

            $userIds = $queryBuilder->pluck('user_id')->toArray();
        }

        if ($teamId) {
            $roleIds = $roles ? (is_array($roles) ? $roles : [$roles]) : null;
            $queryBuilder = \DB::table('user_roles')
                ->where('team_id', $teamId)
                ->where('season_sport_id', $seasonSportId);

            if ($roleIds) {
                $queryBuilder->whereIn('role_id', array_map('intval', $roleIds));
            }

            $userIds = $queryBuilder->pluck('user_id')->toArray();
        }

        if ($clubId || $teamId) {
            if (!empty($userIds)) {
                $query->whereIn('id', $userIds);
            } else {
                return ['rows' => collect([]), 'count' => 0];
            }
        }

        $orderBy = $conditions['order_by'] ?? 'id';
        $orderDirection = $conditions['order_direction'] ?? 'ASC';
        $query->orderBy($orderBy, $orderDirection);

        $limit = $conditions['limit'] ?? 20;
        $page = $conditions['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $count = $query->count();
        $rows = $query->with([
            'roles' => function($q) use ($seasonSportId) {
                $q->wherePivot('season_sport_id', $seasonSportId)
                  ->wherePivot('user_role_approved_by_user_id', '>=', 0);
            },
            'teams' => function($q) use ($seasonSportId) {
                $q->wherePivot('season_sport_id', $seasonSportId);
            },
        ])
        ->select('id', 'email', 'name', 'disable_emails', 'license', 'gender',
                 'birth_year', 'birth_month', 'birth_day', 'nationality',
                 'address_line1', 'address_line2', 'postal_code', 'city',
                 'country', 'phone_numbers', 'debtor_number', 'latlng', 'is_verified')
        ->offset($offset)
        ->limit($limit)
        ->get();

        return ['rows' => $rows, 'count' => $count];
    }

    public function findAllClubUsers(array $conditions = [])
    {
        $clubId = $conditions['club_id'] ?? null;
        $seasonSportId = $conditions['season_sport_id'] ?? null;

        $userIds = [];
        if ($clubId) {
            $userIds = \DB::table('user_roles')
                ->where('club_id', $clubId)
                ->where('season_sport_id', $seasonSportId)
                ->pluck('user_id')
                ->toArray();
        }

        return $this->userRepository->query()
            ->whereIn('id', $userIds)
            ->with(['roles' => function($q) use ($seasonSportId) {
                $q->wherePivot('season_sport_id', $seasonSportId)
                  ->wherePivot('user_role_approved_by_user_id', '>=', 0);
            }])
            ->select('id', 'email', 'name', 'disable_emails', 'license', 'gender',
                     'birth_year', 'birth_month', 'birth_day', 'nationality',
                     'address_line1', 'address_line2', 'postal_code', 'city',
                     'country', 'phone_numbers', 'debtor_number', 'latlng', 'is_verified')
            ->orderBy('name', 'ASC')
            ->get();
    }
}

