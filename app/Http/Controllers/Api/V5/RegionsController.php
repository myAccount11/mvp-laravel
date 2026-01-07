<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\RegionsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionsController extends Controller
{
    protected RegionsService $regionsService;

    public function __construct(RegionsService $regionsService)
    {
        $this->regionsService = $regionsService;
    }

    public function create(Request $request): JsonResponse
    {
        $region = $this->regionsService->create($request->all());
        return response()->json($region, 201);
    }

    public function getAll(): JsonResponse
    {
        $regions = $this->regionsService->findAll();
        return response()->json($regions);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->regionsService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $region = $this->regionsService->findOne([
            'where' => ['id' => $id],
        ]);

        if (!$region) {
            return response()->json(['message' => 'Region not found'], 404);
        }

        return response()->json($region);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->regionsService->update($id, $request->all());
        return response()->json($result);
    }
}

