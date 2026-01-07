<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\CoachLicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoachLicenseController extends Controller
{
    protected CoachLicenseService $coachLicenseService;

    public function __construct(CoachLicenseService $coachLicenseService)
    {
        $this->coachLicenseService = $coachLicenseService;
    }

    public function create(Request $request): JsonResponse
    {
        $coachLicense = $this->coachLicenseService->create($request->all());
        return response()->json($coachLicense, 201);
    }

    public function getAllCoachLicenses(Request $request): JsonResponse
    {
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'asc');
        $coachLicenses = $this->coachLicenseService->findAll($orderBy, $orderDirection);
        return response()->json($coachLicenses);
    }
}

