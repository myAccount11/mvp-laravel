<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateLeagueRequest;
use App\Http\Requests\V5\UpdateLeagueRequest;
use App\Services\V5\LeagueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\V5\Organizer;
use App\Models\V5\Club;
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
        return response()->json($league, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'ASC');

        $conditions = [
            'where' => [],
            'include' => [Organizer::class, Club::class],
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection,
        ];

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
        return response()->json($league);
    }

    public function update(int $id, UpdateLeagueRequest $request): JsonResponse
    {
        $updated = $this->leagueService->update($id, $request->validated());
        if (!$updated) {
            return response()->json(['message' => 'League not found or no changes made'], 404);
        }
        return response()->json(['message' => 'League updated successfully']);
    }
}

