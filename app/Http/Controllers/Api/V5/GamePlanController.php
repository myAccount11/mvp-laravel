<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\GamePlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GamePlanController extends Controller
{
    protected GamePlanService $gamePlanService;

    public function __construct(GamePlanService $gamePlanService)
    {
        $this->gamePlanService = $gamePlanService;
    }

    public function create(Request $request): JsonResponse
    {
        $gamePlan = $this->gamePlanService->create($request->all());
        return response()->json($gamePlan, 201);
    }

    public function findAll(Request $request): JsonResponse
    {
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'asc');
        $gamePlans = $this->gamePlanService->findAll($orderBy, $orderDirection);
        return response()->json($gamePlans);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->gamePlanService->update($id, $request->all());
        return response()->json($result);
    }
}

