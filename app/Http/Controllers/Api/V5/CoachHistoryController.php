<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\CoachHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoachHistoryController extends Controller
{
    protected CoachHistoryService $coachHistoryService;

    public function __construct(CoachHistoryService $coachHistoryService)
    {
        $this->coachHistoryService = $coachHistoryService;
    }

    public function create(Request $request): JsonResponse
    {
        $coachHistory = $this->coachHistoryService->create($request->all());
        return response()->json($coachHistory, 201);
    }

    public function getAllCoachHistories(Request $request): JsonResponse
    {
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'asc');
        $coachHistories = $this->coachHistoryService->findAll($orderBy, $orderDirection);
        return response()->json($coachHistories);
    }

    public function getCoachHistoryById(int $id): JsonResponse
    {
        $coachHistory = $this->coachHistoryService->findOne([
            'where' => ['id' => $id],
        ]);
        return response()->json($coachHistory);
    }
}

