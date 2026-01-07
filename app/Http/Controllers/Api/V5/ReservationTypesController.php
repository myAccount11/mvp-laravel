<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\ReservationTypesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationTypesController extends Controller
{
    protected ReservationTypesService $reservationTypesService;

    public function __construct(ReservationTypesService $reservationTypesService)
    {
        $this->reservationTypesService = $reservationTypesService;
    }

    public function create(Request $request): JsonResponse
    {
        $reservationType = $this->reservationTypesService->create($request->all());
        return response()->json($reservationType, 201);
    }

    public function getAll(): JsonResponse
    {
        $reservationTypes = $this->reservationTypesService->findAll([
            'order' => [['id', 'asc']],
        ]);
        return response()->json($reservationTypes);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->reservationTypesService->delete($id);
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $reservationType = $this->reservationTypesService->findOne([
            'where' => ['id' => $id],
        ]);

        if (!$reservationType) {
            return response()->json(['message' => 'Reservation type not found'], 404);
        }

        return response()->json($reservationType);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->reservationTypesService->update($id, $request->all());
        return response()->json($result);
    }
}

