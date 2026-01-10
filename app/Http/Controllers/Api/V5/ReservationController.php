<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateReservationRequest;
use App\Http\Requests\V5\UpdateReservationRequest;
use App\Services\V5\ReservationService;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    protected ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function create(CreateReservationRequest $request): JsonResponse
    {
        $result = $this->reservationService->createReservation($request->validated());
        if ($result instanceof \Exception) {
            return response()->json(['error' => $result->getMessage()], 400);
        }
        return response()->json($result, 201);
    }

    public function getAll(): JsonResponse
    {
        $orderBy = request('order_by', 'id');
        $orderDirection = request('order_direction', 'asc');
        $reservations = $this->reservationService->findAll([
            'where' => request()->except(['order_by', 'order_direction']),
            'order' => [[$orderBy, $orderDirection]],
        ]);
        return response()->json($reservations);
    }

    public function getById(int $id): JsonResponse
    {
        $reservation = $this->reservationService->findOne(['id' => $id]);
        if (!$reservation) {
            return response()->json(['error' => 'Reservation not found'], 404);
        }
        return response()->json($reservation);
    }

    public function update(int $id, UpdateReservationRequest $request): JsonResponse
    {
        $result = $this->reservationService->updateReservation($id, $request->validated());
        if ($result instanceof \Exception) {
            return response()->json(['error' => $result->getMessage()], 400);
        }
        return response()->json(['success' => $result]);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->reservationService->update($id, ['is_deleted' => true]);
        return response()->json(['success' => $result]);
    }
}

