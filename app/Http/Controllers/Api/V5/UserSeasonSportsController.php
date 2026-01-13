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
        try {
            // If sport_id is provided, find the newest season_sport for that sport
            if ($request->has('sport_id') && !$request->has('season_sport_id')) {
                $userSeasonSport = $this->userSeasonSportsService->createWithSportId($request->all());
            } else {
                $userSeasonSport = $this->userSeasonSportsService->create($request->all());
            }
            
            return response()->json($userSeasonSport, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getAll(Request $request): JsonResponse
    {
        $userId = $request->query('user_id');
        $include = $request->query('include');
        
        $userSeasonSports = $this->userSeasonSportsService->findAllWithFilters($userId, $include);
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

    /**
     * Get user's existing season sports and latest season sports for their sports.
     */
    public function getExistingAndLatest(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $result = $this->userSeasonSportsService->getExistingAndLatestSeasonSports($user->id);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}

