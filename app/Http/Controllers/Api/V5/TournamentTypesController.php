<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\TournamentTypesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TournamentTypesController extends Controller
{
    protected TournamentTypesService $tournamentTypesService;

    public function __construct(TournamentTypesService $tournamentTypesService)
    {
        $this->tournamentTypesService = $tournamentTypesService;
    }

    public function create(Request $request): JsonResponse
    {
        $tournamentType = $this->tournamentTypesService->create($request->all());
        return response()->json($tournamentType, 201);
    }

    public function getAll(): JsonResponse
    {
        $tournamentTypes = $this->tournamentTypesService->findAll();
        return response()->json($tournamentTypes);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->tournamentTypesService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $tournamentType = $this->tournamentTypesService->findOne([
            'where' => ['id' => $id],
        ]);

        if (!$tournamentType) {
            return response()->json(['message' => 'Tournament type not found'], 404);
        }

        return response()->json($tournamentType);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->tournamentTypesService->update($id, $request->all());
        return response()->json($result);
    }
}

