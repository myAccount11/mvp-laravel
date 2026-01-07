<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateTeamRequest;
use App\Http\Requests\V5\UpdateTeamRequest;
use App\Http\Requests\V5\AddUserToTeamRequest;
use App\Services\V5\TeamService;
use App\Services\V5\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    protected $teamService;
    protected $userService;

    public function __construct(TeamService $teamService, UserService $userService)
    {
        $this->teamService = $teamService;
        $this->userService = $userService;
    }

    public function create(CreateTeamRequest $request): JsonResponse
    {
        $team = $this->teamService->create($request->validated());
        
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
        $result = $this->teamService->findAndCountAll($request->all());
        return response()->json($result);
    }

    public function getNames(Request $request): JsonResponse
    {
        $teams = $this->teamService->findAll($request->all());
        return response()->json($teams);
    }

    public function attachGroups($id, Request $request): JsonResponse
    {
        $groups = $request->input('tournament_groups', []);
        $this->teamService->attachGroups($id, $groups);
        return response()->json(['message' => 'Groups attached']);
    }

    public function removeTeamFromTournament($teamTournamentId): JsonResponse
    {
        $this->teamService->removeTeamFromTournament($teamTournamentId);
        return response()->json(['message' => 'Team removed from tournament']);
    }

    public function attachTournament($id, $tournamentId, Request $request): JsonResponse
    {
        $this->teamService->attachTournament($id, $tournamentId, $request->all());
        return response()->json(['message' => 'Tournament attached']);
    }

    public function destroy($id): JsonResponse
    {
        $result = $this->teamService->delete($id);
        return response()->json($result);
    }

    public function show($id): JsonResponse
    {
        $team = $this->teamService->findOne([
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
        $result = $this->userService->findAndCountAllClubOrTeamUsers(array_merge(
            $request->all(),
            ['team_id' => $id]
        ));
        return response()->json($result);
    }

    public function update($id, UpdateTeamRequest $request): JsonResponse
    {
        $result = $this->teamService->update($id, $request->validated());
        return response()->json($result);
    }

    public function addUserToTeam($id, AddUserToTeamRequest $request): JsonResponse
    {
        $user = $request->user();
        $result = $this->teamService->addUserToTeam($id, $request->validated(), $user);
        return response()->json($result);
    }
}

