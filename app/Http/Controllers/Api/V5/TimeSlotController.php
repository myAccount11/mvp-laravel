<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateTimeSlotRequest;
use App\Http\Requests\V5\UpdateTimeSlotRequest;
use App\Services\V5\TimeSlotService;
use App\Services\V5\VenueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TimeSlotController extends Controller
{
    protected TimeSlotService $timeSlotService;
    protected VenueService $venueService;

    public function __construct(TimeSlotService $timeSlotService, VenueService $venueService)
    {
        $this->timeSlotService = $timeSlotService;
        $this->venueService = $venueService;
    }

    public function create(CreateTimeSlotRequest $request): JsonResponse
    {
        $result = $this->timeSlotService->createTimeSlot($request->validated());
        return response()->json($result, 201);
    }

    public function getAll(): JsonResponse
    {
        $result = $this->timeSlotService->findAndCountAll(request()->all());
        return response()->json($result);
    }

    public function getById(int $id): JsonResponse
    {
        $timeSlot = $this->timeSlotService->findOne(['id' => $id]);
        if (!$timeSlot) {
            return response()->json(['error' => 'Time slot not found'], 404);
        }
        return response()->json($timeSlot);
    }

    public function update(int $id, UpdateTimeSlotRequest $request): JsonResponse
    {
        $result = $this->timeSlotService->update($id, $request->validated());
        return response()->json(['success' => $result]);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->timeSlotService->delete($id);
        return response()->json(['success' => $result]);
    }
}

