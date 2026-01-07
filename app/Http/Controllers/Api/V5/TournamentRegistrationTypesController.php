<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\TournamentRegistrationTypesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TournamentRegistrationTypesController extends Controller
{
    protected TournamentRegistrationTypesService $tournamentRegistrationTypesService;

    public function __construct(TournamentRegistrationTypesService $tournamentRegistrationTypesService)
    {
        $this->tournamentRegistrationTypesService = $tournamentRegistrationTypesService;
    }

    public function create(Request $request): JsonResponse
    {
        $tournamentRegistrationType = $this->tournamentRegistrationTypesService->create($request->all());
        return response()->json($tournamentRegistrationType, 201);
    }

    public function getAll(): JsonResponse
    {
        $tournamentRegistrationTypes = $this->tournamentRegistrationTypesService->findAll();
        return response()->json($tournamentRegistrationTypes);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->tournamentRegistrationTypesService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $tournamentRegistrationType = $this->tournamentRegistrationTypesService->findOne([
            'where' => ['id' => $id],
        ]);

        if (!$tournamentRegistrationType) {
            return response()->json(['message' => 'Tournament registration type not found'], 404);
        }

        return response()->json($tournamentRegistrationType);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->tournamentRegistrationTypesService->update($id, $request->all());
        return response()->json($result);
    }
}

