<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateTournamentRequest;
use App\Http\Requests\V5\UpdateTournamentRequest;
use App\Services\V5\TournamentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\V5\Region;
use App\Models\V5\Pool;
use App\Models\V5\Round;
use App\Models\V5\Team;

class TournamentController extends Controller
{
    protected TournamentService $tournamentService;

    public function __construct(TournamentService $tournamentService)
    {
        $this->tournamentService = $tournamentService;
    }

    public function create(CreateTournamentRequest $request): JsonResponse
    {
        $tournament = $this->tournamentService->create($request->validated());
        return response()->json($tournament, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'ASC');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 20);
        $searchTerm = $request->query('searchTerm');

        $conditions = [
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection,
            'page' => $page,
            'limit' => $limit,
            'include' => ['region'],
        ];

        if ($searchTerm) {
            $conditions['searchTerm'] = $searchTerm;
        }

        $result = $this->tournamentService->findAndCountAll($conditions);
        return response()->json($result);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->tournamentService->delete($id);
            if (!$deleted) {
                return response()->json(['message' => 'Tournament not found'], 404);
            }
            return response()->json(['message' => 'Tournament deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getById(int $id): JsonResponse
    {
        $tournament = $this->tournamentService->findOne(['id' => $id]);
        if (!$tournament) {
            return response()->json(['message' => 'Tournament not found'], 404);
        }

        $tournament->load(['region', 'pools' => function ($q) {
            $q->orderBy('id', 'ASC');
        }, 'rounds' => function ($q) {
            $q->orderBy('id', 'ASC');
        }, 'teams']);

        return response()->json($tournament);
    }

    public function update(int $id, UpdateTournamentRequest $request): JsonResponse
    {
        $updated = $this->tournamentService->update($id, $request->validated());
        if (!$updated) {
            return response()->json(['message' => 'Tournament not found or no changes made'], 404);
        }
        return response()->json(['message' => 'Tournament updated successfully']);
    }

    public function getPossibleTeamsForTournament(int $id): JsonResponse
    {
        $teams = $this->tournamentService->getPossibleTeamsForTournament($id);
        return response()->json($teams);
    }
}

