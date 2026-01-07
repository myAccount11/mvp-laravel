<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\UserSeasonSportsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserSeasonSportsController extends Controller
{
    protected UserSeasonSportsService $userSeasonSportsService;

    public function __construct(UserSeasonSportsService $userSeasonSportsService)
    {
        $this->userSeasonSportsService = $userSeasonSportsService;
    }

    public function create(Request $request): JsonResponse
    {
        $userSeasonSport = $this->userSeasonSportsService->create($request->all());
        return response()->json($userSeasonSport, 201);
    }

    public function getAll(): JsonResponse
    {
        $userSeasonSports = $this->userSeasonSportsService->findAll();
        return response()->json($userSeasonSports);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->userSeasonSportsService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $userSeasonSport = $this->userSeasonSportsService->findOne([
            'where' => ['id' => $id],
        ]);

        if (!$userSeasonSport) {
            return response()->json(['message' => 'User Season Sport not found'], 404);
        }

        return response()->json($userSeasonSport);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->userSeasonSportsService->update($id, $request->all());
        return response()->json($result);
    }
}

