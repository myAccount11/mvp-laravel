<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\CoachEducationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoachEducationController extends Controller
{
    protected CoachEducationService $coachEducationService;

    public function __construct(CoachEducationService $coachEducationService)
    {
        $this->coachEducationService = $coachEducationService;
    }

    public function createEducation(Request $request): JsonResponse
    {
        $coachEducation = $this->coachEducationService->createEducation($request->all());
        return response()->json($coachEducation);
    }

    public function getAllCoachEducations(Request $request): JsonResponse
    {
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'asc');
        $coachEducations = $this->coachEducationService->findAll($orderBy, $orderDirection);
        return response()->json($coachEducations);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->coachEducationService->update($id, ['deleted' => true]);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $coachEducation = $this->coachEducationService->findOne([
            'where' => ['id' => $id, 'deleted' => false],
            'include' => ['coachEducationLicenseType', 'coach'],
        ]);

        return response()->json($coachEducation);
    }
}

