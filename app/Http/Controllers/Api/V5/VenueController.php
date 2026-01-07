<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateVenueRequest;
use App\Http\Requests\V5\UpdateVenueRequest;
use App\Services\V5\VenueService;
use Illuminate\Http\JsonResponse;

class VenueController extends Controller
{
    protected VenueService $venueService;

    public function __construct(VenueService $venueService)
    {
        $this->venueService = $venueService;
    }

    public function create(CreateVenueRequest $request): JsonResponse
    {
        $seasonSportId = request('seasonSportId');
        $venue = $this->venueService->createVenue($request->validated(), $seasonSportId);
        return response()->json($venue, 201);
    }

    public function getVenues(): JsonResponse
    {
        $result = $this->venueService->findAndCountAll(request()->all());
        return response()->json($result);
    }

    public function getAllVenues(): JsonResponse
    {
        $seasonSportId = request('seasonSportId');
        $venues = $this->venueService->findAll([
            'include' => ['courts', 'venueSeasonSports'],
            'where' => $seasonSportId ? ['season_sport_id' => $seasonSportId] : [],
        ]);
        return response()->json($venues);
    }

    public function getById(int $id): JsonResponse
    {
        $venue = $this->venueService->findOne(['id' => $id]);
        if (!$venue) {
            return response()->json(['error' => 'Venue not found'], 404);
        }
        return response()->json($venue);
    }

    public function update(int $id, UpdateVenueRequest $request): JsonResponse
    {
        $result = $this->venueService->updateVenue($id, $request->validated());
        return response()->json(['success' => $result]);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->venueService->delete($id);
        return response()->json(['success' => $result]);
    }
}

