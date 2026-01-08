<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateRoundRequest;
use App\Http\Requests\V5\UpdateRoundRequest;
use App\Http\Requests\V5\RecreateRoundRequest;
use App\Services\V5\RoundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoundController extends Controller
{
    protected RoundService $roundService;

    public function __construct(RoundService $roundService)
    {
        $this->roundService = $roundService;
    }

    public function createMany(CreateRoundRequest $request): JsonResponse
    {
        try {
            $rounds = $this->roundService->createMany($request->validated());
            return response()->json($rounds, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateMany(UpdateRoundRequest $request): JsonResponse
    {
        try {
            $updated = $this->roundService->updateMany($request->validated());
            if (!$updated) {
                return response()->json(['message' => 'Failed to update rounds'], 400);
            }
            return response()->json(['message' => 'Rounds updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function recreate(RecreateRoundRequest $request): JsonResponse
    {
        try {
            $rounds = $this->roundService->recreate($request->validated());
            return response()->json($rounds, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getAll(): JsonResponse
    {
        $rounds = $this->roundService->findAll();
        return response()->json($rounds);
    }

    public function deleteGeneratedRounds(Request $request): JsonResponse
    {
        $tournamentId = $request->query('tournament_id');
        if (!$tournamentId) {
            return response()->json(['message' => 'tournament_id is required'], 400);
        }

        $deleted = $this->roundService->destroyByCondition([
            'where' => ['tournament_id' => $tournamentId],
        ]);
        return response()->json(['deleted' => $deleted, 'message' => "Deleted {$deleted} rounds"]);
    }

    public function deleteGeneratedRoundsByIds(Request $request): JsonResponse
    {
        $rounds = $request->query('rounds');
        if (!$rounds || !is_array($rounds)) {
            return response()->json(['message' => 'rounds array is required'], 400);
        }

        $deleted = $this->roundService->destroyByCondition([
            'where' => [['id', 'IN', $rounds]],
        ]);
        return response()->json(['deleted' => $deleted, 'message' => "Deleted {$deleted} rounds"]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->roundService->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Round not found'], 404);
        }
        return response()->json(['message' => 'Round deleted successfully']);
    }

    public function getById(int $id): JsonResponse
    {
        $round = $this->roundService->findOne(['id' => $id]);
        if (!$round) {
            return response()->json(['message' => 'Round not found'], 404);
        }
        return response()->json($round);
    }

    public function update(int $id, UpdateRoundRequest $request): JsonResponse
    {
        $updated = $this->roundService->update($id, $request->validated());
        if (!$updated) {
            return response()->json(['message' => 'Round not found or no changes made'], 404);
        }
        return response()->json(['message' => 'Round updated successfully']);
    }
}

