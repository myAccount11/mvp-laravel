<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\BlockedPeriodsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockedPeriodsController extends Controller
{
    protected BlockedPeriodsService $blockedPeriodsService;

    public function __construct(BlockedPeriodsService $blockedPeriodsService)
    {
        $this->blockedPeriodsService = $blockedPeriodsService;
    }

    public function create(Request $request): JsonResponse
    {
        $blockedPeriod = $this->blockedPeriodsService->create($request->all());
        return response()->json($blockedPeriod, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $queryParams = $request->all();
        $result = $this->blockedPeriodsService->findAndCountAll($queryParams);
        return response()->json($result);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->blockedPeriodsService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $blockedPeriod = $this->blockedPeriodsService->findOne([
            'where' => ['id' => $id],
            'include' => ['tournamentGroups'],
        ]);

        if (!$blockedPeriod) {
            return response()->json(['message' => 'Blocked period not found'], 404);
        }

        return response()->json($blockedPeriod);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->blockedPeriodsService->updatePeriod($id, $request->all());
        return response()->json($result);
    }
}

