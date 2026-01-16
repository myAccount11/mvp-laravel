<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateTournamentRequest;
use App\Http\Requests\V5\UpdateTournamentRequest;
use App\Services\V5\TournamentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $tournament->load(['region', 'league']);
        return response()->json($tournament, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $orderBy = $request->query('order_by', 'id');
        $orderDirection = $request->query('order_direction', 'ASC');
        $page = (int)$request->query('page', 1);
        $limit = (int)$request->query('limit', 20);
        $searchTerm = $request->query('search_term');
        $leagueId = $request->query('league_id');

        $conditions = [
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection,
            'page' => $page,
            'limit' => $limit,
            'include' => ['region', 'league'],
        ];

        if ($searchTerm) {
            $conditions['searchTerm'] = $searchTerm;
        }

        if ($leagueId && $leagueId !== '' && $leagueId !== '0') {
            $conditions['leagueId'] = (int)$leagueId;
        }

        $result = $this->tournamentService->findAndCountAll($conditions);

        return response()->json([
            'rows' => $result['rows'],
            'count' => $result['count']
        ]);
    }

    public function getNames(Request $request): JsonResponse
    {
        $queryParams = $request->all();
        $tournaments = $this->tournamentService->findAll($queryParams);
        return response()->json($tournaments);
    }

    public function getTeamsByTournamentId(int $id): JsonResponse
    {
        $tournament = $this->tournamentService->findOne([
            'where' => ['id' => $id],
            'include' => ['teams', 'league'],
        ]);

        if (!$tournament) {
            return response()->json(['message' => 'Tournament not found'], 404);
        }

        return response()->json($tournament);
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

        $tournament->load(['region', 'league', 'pools' => function ($q) {
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

        $tournament = $this->tournamentService->findOne(['id' => $id]);
        $tournament->load(['region', 'league', 'pools' => function ($q) {
            $q->orderBy('id', 'ASC');
        }, 'rounds' => function ($q) {
            $q->orderBy('id', 'ASC');
        }, 'teams']);

        return response()->json($tournament);
    }

    public function getPossibleTeamsForTournament(int $id): JsonResponse
    {
        $teams = $this->tournamentService->getPossibleTeamsForTournament($id);
        return response()->json($teams);
    }
}

