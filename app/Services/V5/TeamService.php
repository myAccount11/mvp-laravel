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
    protected $teamRepository;
    protected $userService;
    protected $personService;
    protected $playerService;
    protected $messageService;

    public function __construct(
        TeamRepository $teamRepository,
        UserService $userService,
        PersonService $personService,
        PlayerService $playerService,
        MessageService $messageService
    ) {
        $this->teamRepository = $teamRepository;
        $this->userService = $userService;
        $this->personService = $personService;
        $this->playerService = $playerService;
        $this->messageService = $messageService;
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
            $query->where($conditions['where']);
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
                $query->orderBy($order[0], $order[1] ?? 'ASC');
            }
        }

        return $query->get();
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
        $user = $this->userService->findOne(['email' => $data['email']]);
        
        if (!$user) {
            $user = $this->userService->createUser([
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

            $this->personService->syncUserWithPerson($user->id, $data['season_sport_id']);

            if (isset($data['number'])) {
                $player = $this->playerService->findByUserId($user->id);
                if ($player) {
                    DB::table('player')
                        ->where('id', $player->id)
                        ->update(['jersey_number' => $data['number']]);
                }
            }

            if ($authUser->id !== $user->id) {
                $this->messageService->create([
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

