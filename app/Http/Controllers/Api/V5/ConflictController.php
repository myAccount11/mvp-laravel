<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateConflictRequest;
use App\Http\Requests\V5\UpdateConflictRequest;
use App\Services\V5\ConflictService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConflictController extends Controller
{
    protected ConflictService $conflictService;

    public function __construct(ConflictService $conflictService)
    {
        $this->conflictService = $conflictService;
    }

    public function create(CreateConflictRequest $request): JsonResponse
    {
        $conflict = $this->conflictService->create($request->validated());
        return response()->json($conflict, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'ASC');
        $query = $request->except(['orderBy', 'orderDirection']);

        $conditions = [
            'where' => $query,
            'order' => [[$orderBy, $orderDirection]],
        ];

        $conflicts = $this->conflictService->findAll($conditions);
        return response()->json($conflicts);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->conflictService->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Conflict not found'], 404);
        }
        return response()->json(['message' => 'Conflict deleted successfully']);
    }

    public function getById(int $id): JsonResponse
    {
        $conflict = $this->conflictService->findOne(['id' => $id]);
        if (!$conflict) {
            return response()->json(['message' => 'Conflict not found'], 404);
        }
        return response()->json($conflict);
    }

    public function update(int $id, UpdateConflictRequest $request): JsonResponse
    {
        $updated = $this->conflictService->update($id, $request->validated());
        if (!$updated) {
            return response()->json(['message' => 'Conflict not found or no changes made'], 404);
        }
        return response()->json(['message' => 'Conflict updated successfully']);
    }
}

