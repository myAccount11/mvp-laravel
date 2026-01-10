<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\TournamentProgramItemsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TournamentProgramItemsController extends Controller
{
    protected TournamentProgramItemsService $tournamentProgramItemsService;

    public function __construct(TournamentProgramItemsService $tournamentProgramItemsService)
    {
        $this->tournamentProgramItemsService = $tournamentProgramItemsService;
    }

    public function create(Request $request): JsonResponse
    {
        $tournamentProgramItem = $this->tournamentProgramItemsService->create($request->all());
        return response()->json($tournamentProgramItem, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $conditions = [];
        
        if ($request->has('tournament_program_id')) {
            $conditions['where'] = ['tournament_program_id' => $request->input('tournament_program_id')];
        }
        
        $tournamentProgramItems = $this->tournamentProgramItemsService->findAll($conditions);
        return response()->json($tournamentProgramItems);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->tournamentProgramItemsService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $tournamentProgramItem = $this->tournamentProgramItemsService->findOne([
            'where' => ['id' => $id],
        ]);

        if (!$tournamentProgramItem) {
            return response()->json(['message' => 'Tournament program item not found'], 404);
        }

        return response()->json($tournamentProgramItem);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->tournamentProgramItemsService->update($id, $request->all());
        return response()->json($result);
    }
}

