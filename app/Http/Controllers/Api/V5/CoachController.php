<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateCoachRequest;
use App\Http\Requests\V5\CreateCoachByNameEmailRequest;
use App\Http\Requests\V5\CoachInfoRequest;
use App\Services\V5\CoachService;
use Illuminate\Http\JsonResponse;

class CoachController extends Controller
{
    protected CoachService $coachService;

    public function __construct(CoachService $coachService)
    {
        $this->coachService = $coachService;
    }

    public function create(CreateCoachRequest $request): JsonResponse
    {
        $coach = $this->coachService->create($request->validated());
        return response()->json($coach, 201);
    }

    public function getAllCoaches(): JsonResponse
    {
        $result = $this->coachService->findAllCoachesByFiltersOrWithout(request()->all());
        return response()->json($result);
    }

    public function createCoach(CreateCoachByNameEmailRequest $request): JsonResponse
    {
        $personId = $this->coachService->createCoach($request->validated());
        return response()->json(['person_id' => $personId], 201);
    }

    public function coachInfo(CoachInfoRequest $request): JsonResponse
    {
        $result = $this->coachService->coachInfo($request->validated());
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $coach = $this->coachService->findOneCoach(['id' => $id]);
        if (!$coach) {
            return response()->json(['error' => 'Coach not found'], 404);
        }
        return response()->json($coach);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->coachService->delete($id);
        return response()->json(['success' => $result]);
    }
}

