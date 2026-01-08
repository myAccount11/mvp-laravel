<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateLeagueRequest;
use App\Http\Requests\V5\UpdateLeagueRequest;
use App\Services\V5\LeagueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeagueController extends Controller
{
    protected LeagueService $leagueService;

    public function __construct(LeagueService $leagueService)
    {
        $this->leagueService = $leagueService;
    }

    public function create(CreateLeagueRequest $request): JsonResponse
    {
        $league = $this->leagueService->create($request->validated());
        $league->load(['organizer', 'club']);
        return response()->json($league, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $orderBy = $request->query('order_by', 'id');
        $orderDirection = $request->query('order_direction', 'ASC');
        $seasonSportId = $request->query('season_sport_id');
        $deleted = $request->query('deleted');

        $conditions = [
            'where' => [],
            'include' => ['organizer', 'club'],
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection,
        ];

        // Handle deleted filter
        if ($deleted !== null) {
            $deletedValue = filter_var($deleted, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($deletedValue !== null) {
                $conditions['where'][] = ['deleted', '=', $deletedValue];
            }
        } else {
            // Default to false if not specified
            $conditions['where'][] = ['deleted', '=', false];
        }

        // Handle seasonSportId filter
        if ($seasonSportId !== null && $seasonSportId !== '' && $seasonSportId != '0' && $seasonSportId != 0) {
            $conditions['where'][] = ['season_sport_id', '=', (int)$seasonSportId];
        }

        $user = Auth::user();
        if ($user) {
            $isSuperAdmin = $user->roles->contains('description', 'SUPER_ADMIN');
            if (!$isSuperAdmin) {
                $isAssociationAdmin = $user->roles->contains('description', 'ASSOCIATION_ADMIN');
                if ($isAssociationAdmin) {
                    $conditions['where'][] = ['is_active', '=', true];
                } else {
                    $clubIds = $user->userRoles->where('club_id', '!=', null)->pluck('club_id')->toArray();
                    if (!empty($clubIds)) {
                        $conditions['where'][] = function ($query) use ($clubIds) {
                            $query->where('is_active', true)
                                  ->orWhereIn('club_id', $clubIds);
                        };
                    } else {
                        $conditions['where'][] = ['is_active', '=', true];
                    }
                }
            }
        }

        $leagues = $this->leagueService->findAll($conditions);
        
        return response()->json($leagues);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->leagueService->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'League not found'], 404);
        }
        return response()->json(['message' => 'League deleted successfully']);
    }

    public function getById(int $id): JsonResponse
    {
        $league = $this->leagueService->findOne(['id' => $id]);
        if (!$league) {
            return response()->json(['message' => 'League not found'], 404);
        }
        $league->load(['organizer', 'club']);
        return response()->json($league);
    }

    public function update(int $id, UpdateLeagueRequest $request): JsonResponse
    {
        $updated = $this->leagueService->update($id, $request->validated());
        if (!$updated) {
            return response()->json(['message' => 'League not found or no changes made'], 404);
        }
        
        $league = $this->leagueService->findOne(['id' => $id]);
        $league->load(['organizer', 'club']);
        return response()->json($league);
    }
}

