<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
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

    public function create(Request $request): JsonResponse
    {
        $tournamentGroup = $this->tournamentGroupService->create($request->all());
        return response()->json($tournamentGroup, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $queryParams = $request->all();
        $result = $this->tournamentGroupService->findAndCountAll($queryParams);
        return response()->json($result);
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

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->tournamentGroupService->update($id, $request->all());
        return response()->json($result);
    }
}

