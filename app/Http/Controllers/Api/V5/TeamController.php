<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateTeamRequest;
use App\Http\Requests\V5\UpdateTeamRequest;
use App\Http\Requests\V5\AddUserToTeamRequest;
use App\Services\V5\TeamService;
use App\Services\V5\UserService;
use App\Repositories\V5\TeamRepository;
use App\Models\V5\Team;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    protected ?TeamService $teamService = null;
    protected ?UserService $userService = null;

    public function __construct()
    {
        // Dependencies are lazy-loaded to avoid circular dependency issues
    }

    protected function getTeamService(): TeamService
    {
        return $this->teamService ??= app(TeamService::class);
    }

    protected function getUserService(): UserService
    {
        return $this->userService ??= app(UserService::class);
    }

    public function create(CreateTeamRequest $request): JsonResponse
    {
        $team = $this->getTeamService()->create($request->validated());

        if ($request->input('season_sport_id')) {
            DB::table('team_season_sports')->insert([
                'team_id' => $team->id,
                'season_sport_id' => $request->input('season_sport_id'),
            ]);
        }

        return response()->json($team, 201);
    }

    public function index(Request $request): JsonResponse
    {
        $result = $this->getTeamService()->findAndCountAll($request->all());
        return response()->json($result);
    }

    public function getNames(Request $request): JsonResponse
    {
        $teams = $this->getTeamService()->findAll($request->all());
        return response()->json($teams);
    }

    public function attachGroups($id, Request $request): JsonResponse
    {
        // Accept both snake_case and camelCase, and also accept just an array directly
        $groups = $request->input('tournament_groups',
            $request->input('tournamentGroups',
                is_array($request->all()) && !$request->has('tournament_groups') && !$request->has('tournamentGroups')
                    ? $request->all()
                    : []
            )
        );
        $this->getTeamService()->attachGroups($id, $groups);
        return response()->json(['message' => 'Groups attached']);
    }

    public function removeTeamFromTournament($teamTournamentId): JsonResponse
    {
        $this->getTeamService()->removeTeamFromTournament($teamTournamentId);
        return response()->json(['message' => 'Team removed from tournament']);
    }

    public function attachTournament($id, $tournamentId, Request $request): JsonResponse
    {
        // Convert poolId to pool_id for database
        $data = $request->all();
        if (isset($data['poolId'])) {
            $data['pool_id'] = $data['poolId'];
            unset($data['poolId']);
        }
        $this->getTeamService()->attachTournament($id, $tournamentId, $data);
        return response()->json(['message' => 'Tournament attached']);
    }

    public function destroy($id): JsonResponse
    {
        $result = $this->getTeamService()->delete($id);
        return response()->json($result);
    }

    public function show($id): JsonResponse
    {
        $team = $this->getTeamService()->findOne([
            'where' => ['id' => $id],
            'include' => ['tournamentGroups', 'tournaments'],
        ]);

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        return response()->json($team);
    }

    public function getTeamUsers($id, Request $request): JsonResponse
    {
        $result = $this->getUserService()->findAndCountAllClubOrTeamUsers(array_merge(
            $request->all(),
            ['team_id' => $id]
        ));
        return response()->json($result);
    }

    public function update($id, UpdateTeamRequest $request): JsonResponse
    {
        $result = $this->getTeamService()->update($id, $request->validated());
        return response()->json($result);
    }

    public function addUserToTeam($id, AddUserToTeamRequest $request): JsonResponse
    {
        $user = $request->user();
        $result = $this->getTeamService()->addUserToTeam($id, $request->validated(), $user);
        return response()->json($result);
    }
}

