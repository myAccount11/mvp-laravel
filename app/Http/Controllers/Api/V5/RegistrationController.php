<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    protected RegistrationService $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function create(Request $request): JsonResponse
    {
        $registration = $this->registrationService->create($request->all());
        return response()->json($registration, 201);
    }

    public function saveBulkRegistration(Request $request): JsonResponse
    {
        $result = $this->registrationService->saveBulkRegistration($request->all());
        return response()->json($result);
    }

    public function getRegistrations(Request $request): JsonResponse
    {
        $queryParams = $request->all();
        $result = $this->registrationService->findAndCountAll($queryParams);
        return response()->json($result);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->registrationService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $registration = $this->registrationService->findOne([
            'where' => ['id' => $id],
        ]);

        if (!$registration) {
            return response()->json(['message' => "Registration with id {$id} not found"], 404);
        }

        return response()->json($registration);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->registrationService->update($id, $request->all());
        return response()->json($result);
    }
}

