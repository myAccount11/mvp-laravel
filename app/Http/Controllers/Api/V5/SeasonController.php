<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateSeasonRequest;
use App\Http\Requests\V5\UpdateSeasonRequest;
use App\Services\V5\SeasonService;
use Illuminate\Http\JsonResponse;

class SeasonController extends Controller
{
    protected SeasonService $seasonService;

    public function __construct(SeasonService $seasonService)
    {
        $this->seasonService = $seasonService;
    }

    public function create(CreateSeasonRequest $request): JsonResponse
    {
        $season = $this->seasonService->create($request->validated());
        return response()->json($season, 201);
    }

    public function getAll(): JsonResponse
    {
        $seasons = $this->seasonService->findAll();
        return response()->json($seasons);
    }

    public function getById(int $id): JsonResponse
    {
        $season = $this->seasonService->findOne(['id' => $id]);
        if (!$season) {
            return response()->json(['error' => 'Season not found'], 404);
        }
        return response()->json($season);
    }

    public function update(int $id, UpdateSeasonRequest $request): JsonResponse
    {
        $result = $this->seasonService->update($id, $request->validated());
        return response()->json(['success' => $result]);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->seasonService->delete($id);
        return response()->json(['success' => $result]);
    }
}

