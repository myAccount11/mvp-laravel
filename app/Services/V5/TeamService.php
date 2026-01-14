<?php

namespace App\Services\V5;

use App\Repositories\V5\TeamRepository;
use App\Services\V5\UserService;
use App\Services\V5\PersonService;
use App\Services\V5\PlayerService;
use App\Services\V5\MessageService;
use Illuminate\Support\Facades\DB;
use App\Models\V5\User;

class TeamService
{
    protected ?UserService $userService = null;
    protected ?PersonService $personService = null;
    protected ?PlayerService $playerService = null;
    protected ?MessageService $messageService = null;

    public function __construct(protected TeamRepository $teamRepository)
    {
    }

    protected function getUserService(): UserService
    {
        return $this->userService ??= app(UserService::class);
    }

    protected function getPersonService(): PersonService
    {
        return $this->personService ??= app(PersonService::class);
    }

    protected function getPlayerService(): PlayerService
    {
        return $this->playerService ??= app(PlayerService::class);
    }

    protected function getMessageService(): MessageService
    {
        return $this->messageService ??= app(MessageService::class);
    }

    public function create(array $data)
    {
        $lastLicense = $this->teamRepository->getLastLicense();
        return $this->teamRepository->create(array_merge($data, [
            'license' => $lastLicense + 1,
        ]));
    }

    public function findAndCountAll(array $conditions = [])
    {
        return $this->teamRepository->findAndCountAll($conditions);
    }

    public function findAll(array $conditions = [])
    {
        $query = $this->teamRepository->query();

        if (isset($conditions['where'])) {
            if (is_array($conditions['where'])) {
                // Handle array of conditions like [['id', 'not in', $array], ['id', 'in', $array]]
                foreach ($conditions['where'] as $whereCondition) {
                    if (is_array($whereCondition)) {
                        if (count($whereCondition) === 3) {
                            $column = $whereCondition[0];
                            $operator = strtolower($whereCondition[1]);
                            $value = $whereCondition[2];

                            if ($operator === 'not in') {
                                $query->whereNotIn($column, is_array($value) ? $value : [$value]);
                            } elseif ($operator === 'in') {
                                $query->whereIn($column, is_array($value) ? $value : [$value]);
                            } else {
                                $query->where($column, $operator, $value);
                            }
                        } elseif (count($whereCondition) === 2) {
                            $query->where($whereCondition[0], $whereCondition[1]);
                        }
                    } else {
                        // Handle simple key-value pairs
                        $query->where($whereCondition);
                    }
                }
            } else {
                // Handle simple where condition
                $query->where($conditions['where']);
            }
        }

        if (isset($conditions['include'])) {
            foreach ($conditions['include'] as $include) {
                $query->with($include);
            }
        }

        if (isset($conditions['attributes'])) {
            $query->select($conditions['attributes']);
        }

        if (isset($conditions['order'])) {
            foreach ($conditions['order'] as $order) {
                // Normalize camelCase to snake_case for order column
                $orderColumn = $this->normalizeOrderBy($order[0]);
                $query->orderBy($orderColumn, $order[1] ?? 'ASC');
            }
        }

        // Also handle order_by if provided
        if (isset($conditions['order_by'])) {
            $orderBy = $this->normalizeOrderBy($conditions['order_by']);
            $orderDirection = $conditions['order_direction'] ?? 'ASC';
            $query->orderBy($orderBy, $orderDirection);
        }

        return $query->get();
    }

    /**
     * Normalize camelCase order_by parameter to snake_case
     */
    protected function normalizeOrderBy(string $orderBy): string
    {
        $fieldMap = [
            'tournamentName' => 'tournament_name',
            'localName' => 'local_name',
            'clubId' => 'club_id',
            'ageGroup' => 'age_group',
            'officialTypeId' => 'official_type_id',
            'officialTeamId' => 'official_team_id',
            'clubRank' => 'club_rank',
        ];

        return $fieldMap[$orderBy] ?? $orderBy;
    }

    public function findOne(array $conditions)
    {
        $query = $this->teamRepository->query();

        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }

        if (isset($conditions['include'])) {
            foreach ($conditions['include'] as $include) {
                $query->with($include);
            }
        }

        return $query->first();
    }

    public function update($id, array $data)
    {
        return $this->teamRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->teamRepository->delete($id);
    }

    public function attachGroups($id, array $groups)
    {
        DB::table('team_tournament_groups')
            ->where('team_id', $id)
            ->whereNotIn('tournament_group_id', $groups)
            ->delete();

        $existing = DB::table('team_tournament_groups')
            ->where('team_id', $id)
            ->whereIn('tournament_group_id', $groups)
            ->pluck('tournament_group_id')
            ->toArray();

        $newGroups = array_diff($groups, $existing);
        $data = [];
        foreach ($newGroups as $groupId) {
            $data[] = [
                'team_id' => $id,
                'tournament_group_id' => $groupId,
            ];
        }

        if (count($data) > 0) {
            DB::table('team_tournament_groups')->insert($data);
        }

        return true;
    }

    public function attachTournament($id, $tournamentId, array $data = [])
    {
        // Check if team is already attached to this tournament
        $existing = DB::table('team_tournaments')
            ->where('team_id', $id)
            ->where('tournament_id', $tournamentId)
            ->where('is_deleted', false)
            ->first();

        if ($existing) {
            // Update existing record with new pool_id and other data
            DB::table('team_tournaments')
                ->where('id', $existing->id)
                ->update($data);
            return true;
        }

        // Create new record if not already attached
        return DB::table('team_tournaments')->insert(array_merge([
            'team_id' => $id,
            'tournament_id' => $tournamentId,
        ], $data));
    }

    public function removeTeamFromTournament($id)
    {
        return DB::table('team_tournaments')->where('id', $id)->delete();
    }

    public function addUserToTeam($teamId, array $data, User $authUser)
    {
        $user = $this->getUserService()->findOne(['email' => $data['email']]);

        if (!$user) {
            $user = $this->getUserService()->createUser([
                'name' => $data['name'],
                'email' => $data['email'],
                'disable_emails' => false,
                'password' => '',
            ]);
        }

        if ($user->id) {
            $team = $this->teamRepository->find($teamId);
            $team->load('club');

            $userRole = DB::table('user_roles')
                ->where('user_id', $user->id)
                ->where('role_id', 8)
                ->where('team_id', $team->id)
                ->first();

            if ($userRole) {
                DB::table('user_roles')
                    ->where('id', $userRole->id)
                    ->update(['user_role_approved_by_user_id' => $authUser->id]);
            } else {
                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role_id' => 8,
                    'team_id' => $team->id,
                    'club_id' => $team->club_id,
                    'season_sport_id' => $data['season_sport_id'],
                    'user_role_approved_by_user_id' => $authUser->id,
                ]);
            }

            $this->getPersonService()->syncUserWithPerson($user->id, $data['season_sport_id']);

            if (isset($data['number'])) {
                $player = $this->getPlayerService()->findByUserId($user->id);
                if ($player) {
                    DB::table('player')
                        ->where('id', $player->id)
                        ->update(['jersey_number' => $data['number']]);
                }
            }

            if ($authUser->id !== $user->id) {
                $this->getMessageService()->create([
                    'type_id' => 1,
                    'to_id' => $user->id,
                    'user_id' => 1473,
                    'subject' => 'You are registered as a player.',
                    'html' => "You are registered as a player in MVP on the team <strong>{$team->local_name}</strong> in the club <strong>{$team->club->name}</strong> by <strong>{$authUser->name}</strong>.<br /><br />With the MVP App you can see your match schedule, follow your favorites, see live scoring and much more<br /><br />Download it in the App Store or Google Play<br /><br />Sincerely, MVP App",
                ]);
            }
        }

        return $user;
    }
}

