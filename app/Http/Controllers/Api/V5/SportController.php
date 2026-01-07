<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateSportRequest;
use App\Http\Requests\V5\UpdateSportRequest;
use App\Services\V5\SportService;
use Illuminate\Http\JsonResponse;

class SportController extends Controller
{
    protected SportService $sportService;

    public function __construct(SportService $sportService)
    {
        $this->sportService = $sportService;
    }

    public function create(CreateSportRequest $request): JsonResponse
    {
        try {
            $sport = $this->sportService->create($request->validated());
            return response()->json($sport, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getSports(): JsonResponse
    {
        $orderBy = request('orderBy', 'id');
        $orderDirection = request('orderDirection', 'asc');
        $sports = $this->sportService->findAll($orderBy, $orderDirection);
        return response()->json($sports);
    }

    public function getById(int $id): JsonResponse
    {
        $sport = $this->sportService->findOne(['id' => $id]);
        if (!$sport) {
            return response()->json(['error' => 'Sport not found'], 404);
        }
        return response()->json($sport);
    }

    public function update(int $id, UpdateSportRequest $request): JsonResponse
    {
        try {
            $result = $this->sportService->update($id, $request->validated());
            if (!$result) {
                return response()->json(['error' => 'Sport not found'], 404);
            }
            return response()->json(['success' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function remove(int $id): JsonResponse
    {
        $result = $this->sportService->delete($id);
        if (!$result) {
            return response()->json(['error' => 'Sport not found'], 404);
        }
        return response()->json(['success' => $result]);
    }
}

