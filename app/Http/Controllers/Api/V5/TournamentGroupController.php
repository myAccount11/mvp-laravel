<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateTournamentGroupRequest;
use App\Http\Requests\V5\UpdateTournamentGroupRequest;
use App\Services\V5\TournamentGroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TournamentGroupController extends Controller
{
    protected TournamentGroupService $tournamentGroupService;

    public function __construct(TournamentGroupService $tournamentGroupService)
    {
        $this->tournamentGroupService = $tournamentGroupService;
    }

    public function create(CreateTournamentGroupRequest $request): JsonResponse
    {
        $tournamentGroup = $this->tournamentGroupService->create($request->validated());
        $tournamentGroup->load(['league', 'tournamentConfig']);
        return response()->json($tournamentGroup, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $orderBy = $request->query('order_by', 'id');
        $orderDirection = $request->query('order_direction', 'ASC');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 20);
        $searchTerm = $request->query('search_term');
        $leagueId = $request->query('league_id');

        $conditions = [
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection,
            'page' => (int)$page,
            'limit' => (int)$limit,
        ];

        if ($searchTerm && trim($searchTerm) !== '') {
            $conditions['searchTerm'] = trim($searchTerm);
        }

        if ($leagueId && $leagueId !== '' && $leagueId !== '0') {
            $conditions['leagueId'] = (int)$leagueId;
        }

        $result = $this->tournamentGroupService->findAndCountAll($conditions);
        
        return response()->json([
            'rows' => $result['rows'],
            'count' => $result['count']
        ]);
    }

    public function getNames(Request $request): JsonResponse
    {
        $queryParams = $request->all();
        $tournamentGroups = $this->tournamentGroupService->findAll($queryParams);
        return response()->json($tournamentGroups);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->tournamentGroupService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $tournamentGroup = $this->tournamentGroupService->findOne([
            'where' => ['id' => $id],
            'include' => ['league', 'tournamentConfig'],
        ]);

        if (!$tournamentGroup) {
            return response()->json(['message' => 'Tournament group not found'], 404);
        }

        return response()->json($tournamentGroup);
    }

    public function getTeamsByGroupId(int $id): JsonResponse
    {
        $tournamentGroup = $this->tournamentGroupService->findOne([
            'where' => ['id' => $id],
            'include' => ['teams', 'league'],
        ]);

        if (!$tournamentGroup) {
            return response()->json(['message' => 'Tournament group not found'], 404);
        }

        return response()->json($tournamentGroup);
    }

    public function getPossibleTeamsForGroup(int $id): JsonResponse
    {
        $teams = $this->tournamentGroupService->getPossibleTeamsForGroup($id);
        return response()->json($teams);
    }

    public function update(int $id, UpdateTournamentGroupRequest $request): JsonResponse
    {
        $result = $this->tournamentGroupService->update($id, $request->validated());
        if ($result) {
            $tournamentGroup = $this->tournamentGroupService->findOne([
                'where' => ['id' => $id],
                'include' => ['league', 'tournamentConfig'],
            ]);
            return response()->json($tournamentGroup);
        }
        return response()->json(['message' => 'Tournament group not found or update failed'], 404);
    }
}

