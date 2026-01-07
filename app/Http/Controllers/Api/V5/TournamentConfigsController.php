<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\TournamentConfigsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TournamentConfigsController extends Controller
{
    protected TournamentConfigsService $tournamentConfigsService;

    public function __construct(TournamentConfigsService $tournamentConfigsService)
    {
        $this->tournamentConfigsService = $tournamentConfigsService;
    }

    public function create(Request $request): JsonResponse
    {
        $tournamentConfig = $this->tournamentConfigsService->create($request->all());
        return response()->json($tournamentConfig, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $queryParams = $request->all();
        $result = $this->tournamentConfigsService->findAndCountAll($queryParams);
        return response()->json($result);
    }

    public function getNames(Request $request): JsonResponse
    {
        $queryParams = $request->all();
        $tournamentConfigs = $this->tournamentConfigsService->findAll($queryParams);
        return response()->json($tournamentConfigs);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->tournamentConfigsService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $tournamentConfig = $this->tournamentConfigsService->findOne([
            'where' => ['id' => $id],
        ]);

        if (!$tournamentConfig) {
            return response()->json(['message' => 'Tournament config not found'], 404);
        }

        return response()->json($tournamentConfig);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->tournamentConfigsService->update($id, $request->all());
        return response()->json($result);
    }
}

