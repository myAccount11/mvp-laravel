<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\CourtPriorityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourtPriorityController extends Controller
{
    protected CourtPriorityService $courtPriorityService;

    public function __construct(CourtPriorityService $courtPriorityService)
    {
        $this->courtPriorityService = $courtPriorityService;
    }

    public function create(Request $request): JsonResponse
    {
        $courtPriority = $this->courtPriorityService->create($request->all());
        return response()->json($courtPriority);
    }

    public function createBulk(Request $request): JsonResponse
    {
        $clubId = $request->query('clubId');
        $courtPriorities = $request->all();
        $result = $this->courtPriorityService->createAndUpdate($courtPriorities, $clubId);
        return response()->json($result);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->courtPriorityService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $courtPriority = $this->courtPriorityService->findOne([
            'where' => ['id' => $id],
        ]);

        if (!$courtPriority) {
            return response()->json(['message' => "court priority with id {$id} not found"], 404);
        }

        return response()->json($courtPriority);
    }
}

