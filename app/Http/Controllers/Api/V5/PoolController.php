<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreatePoolRequest;
use App\Http\Requests\V5\UpdatePoolRequest;
use App\Services\V5\PoolService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PoolController extends Controller
{
    protected PoolService $poolService;

    public function __construct(PoolService $poolService)
    {
        $this->poolService = $poolService;
    }

    public function createMany(CreatePoolRequest $request): JsonResponse
    {
        $pools = $this->poolService->createMany($request->validated());
        return response()->json($pools, 201);
    }

    public function createOrUpdate(int $tournamentId, CreatePoolRequest $request): JsonResponse
    {
        $pools = $this->poolService->createOrUpdate($tournamentId, $request->validated());
        return response()->json($pools, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $conditions = [];
        $tournamentId = $request->query('tournament_id');
        if ($tournamentId) {
            $conditions['where'] = ['tournament_id' => $tournamentId];
        }
        $pools = $this->poolService->findAll($conditions);
        return response()->json($pools);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->poolService->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Pool not found'], 404);
        }
        return response()->json(['message' => 'Pool deleted successfully']);
    }

    public function getById(int $id): JsonResponse
    {
        $pool = $this->poolService->findOne(['id' => $id]);
        if (!$pool) {
            return response()->json(['message' => 'Pool not found'], 404);
        }
        return response()->json($pool);
    }

    public function update(int $id, UpdatePoolRequest $request): JsonResponse
    {
        $updated = $this->poolService->update($id, $request->validated());
        if (!$updated) {
            return response()->json(['message' => 'Pool not found or no changes made'], 404);
        }
        return response()->json(['message' => 'Pool updated successfully']);
    }
}

