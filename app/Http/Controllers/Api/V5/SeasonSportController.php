<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\SeasonSportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeasonSportController extends Controller
{
    protected SeasonSportService $seasonSportService;

    public function __construct(SeasonSportService $seasonSportService)
    {
        $this->seasonSportService = $seasonSportService;
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $seasonSport = $this->seasonSportService->create($request->all());
            return response()->json($seasonSport, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function findAll(): JsonResponse
    {
        $seasonSports = $this->seasonSportService->findAll();
        return response()->json($seasonSports);
    }

    public function getById(int $id): JsonResponse
    {
        $seasonSport = $this->seasonSportService->findOne([
            'where' => ['id' => $id],
        ]);

        if (!$seasonSport) {
            return response()->json(['message' => 'Season-sport not found'], 404);
        }

        return response()->json($seasonSport);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->seasonSportService->update($id, $request->all());
        return response()->json($result);
    }

    public function remove(int $id): JsonResponse
    {
        $deleted = $this->seasonSportService->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Season-sport not found'], 404);
        }
        return response()->json($deleted);
    }
}

