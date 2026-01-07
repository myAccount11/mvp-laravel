<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\TournamentStructuresService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TournamentStructuresController extends Controller
{
    protected TournamentStructuresService $tournamentStructuresService;

    public function __construct(TournamentStructuresService $tournamentStructuresService)
    {
        $this->tournamentStructuresService = $tournamentStructuresService;
    }

    public function create(Request $request): JsonResponse
    {
        $tournamentStructure = $this->tournamentStructuresService->create($request->all());
        return response()->json($tournamentStructure, 201);
    }

    public function getAll(): JsonResponse
    {
        $tournamentStructures = $this->tournamentStructuresService->findAll();
        return response()->json($tournamentStructures);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->tournamentStructuresService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $tournamentStructure = $this->tournamentStructuresService->findOne([
            'where' => ['id' => $id],
        ]);

        if (!$tournamentStructure) {
            return response()->json(['message' => 'Tournament structure type not found'], 404);
        }

        return response()->json($tournamentStructure);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->tournamentStructuresService->update($id, $request->all());
        return response()->json($result);
    }
}

