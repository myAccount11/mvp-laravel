<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateVenueRequest;
use App\Http\Requests\V5\UpdateVenueRequest;
use App\Http\Requests\V5\GetAllVenuesRequest;
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
        $venueData = collect($request->validated())
            ->except('season_sport_id')
            ->toArray();

        $venue = $this->venueService->createVenue(
            $venueData,
            $request->getSeasonSportId()
        );
        return response()->json($venue, 201);
    }

    public function getVenues(): JsonResponse
    {
        $result = $this->venueService->findAndCountAll(request()->all());
        return response()->json($result);
    }

    public function getAllVenues(GetAllVenuesRequest $request): JsonResponse
    {
        $seasonSportId = $request->validated()['season_sport_id'] ?? null;
        
        $venues = $this->venueService->findAll([
            'include' => ['courts', 'venueSeasonSports'],
            'season_sport_id' => $seasonSportId ? (int)$seasonSportId : null,
        ]);
        
        return response()->json($venues);
    }

    public function getById(int $id): JsonResponse
    {
        $venue = $this->venueService->findOne(['id' => $id], ['courts', 'clubs']);
        if (!$venue) {
            return response()->json(['error' => 'Venue not found'], 404);
        }
        return response()->json($venue);
    }

    public function update(int $id, UpdateVenueRequest $request): JsonResponse
    {
        $result = $this->venueService->updateVenue($id, $request->validated());

        // Check if there's an error message
        if (isset($result['message']) && $result['message'] !== 'Success') {
            $statusCode = match(true) {
                str_contains($result['message'], 'not found') => 404,
                str_contains($result['message'], 'already associated') => 400,
                str_contains($result['message'], 'Error') => 500,
                default => 400,
            };
            return response()->json(['error' => $result['message']], $statusCode);
        }

        return response()->json($result, 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->venueService->delete($id);
        return response()->json(['success' => $result]);
    }
}

