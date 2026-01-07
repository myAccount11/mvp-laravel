<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\TournamentProgramsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TournamentProgramsController extends Controller
{
    protected TournamentProgramsService $tournamentProgramsService;

    public function __construct(TournamentProgramsService $tournamentProgramsService)
    {
        $this->tournamentProgramsService = $tournamentProgramsService;
    }

    public function create(Request $request): JsonResponse
    {
        $tournamentProgram = $this->tournamentProgramsService->create($request->all());
        return response()->json($tournamentProgram, 201);
    }

    public function getAll(): JsonResponse
    {
        $tournamentPrograms = $this->tournamentProgramsService->findAll(['order' => [['name', 'ASC']]]);
        return response()->json($tournamentPrograms);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->tournamentProgramsService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $tournamentProgram = $this->tournamentProgramsService->findOne([
            'where' => ['id' => $id],
        ]);

        if (!$tournamentProgram) {
            return response()->json(['message' => 'Tournament program not found'], 404);
        }

        return response()->json($tournamentProgram);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->tournamentProgramsService->update($id, $request->all());
        return response()->json($result);
    }
}

