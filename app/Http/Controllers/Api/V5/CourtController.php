<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\CourtService;
use App\Models\V5\VenueSeasonSport;
use App\Models\V5\ClubVenues;
use App\Models\V5\Court;
use App\Models\V5\Venue;
use Illuminate\Http\JsonResponse;

class CourtController extends Controller
{
    protected CourtService $courtService;

    public function __construct(CourtService $courtService)
    {
        $this->courtService = $courtService;
    }

    public function getAll(): JsonResponse
    {
        $limit = request('limit', 10);
        $offset = request('offset', 0);
        $result = $this->courtService->getCourts($limit, $offset);
        return response()->json($result);
    }

    public function getCourtsForFilter(): JsonResponse
    {
        $seasonSportId = request('seasonSportId') ?? request('season_sport_id');
        
        if (!$seasonSportId) {
            return response()->json(['error' => 'seasonSportId is required'], 400);
        }
        
        $venueSeasonSports = VenueSeasonSport::where('season_sport_id', $seasonSportId)->get();
        
        $venueIds = $venueSeasonSports->pluck('venue_id')->toArray();
        
        if (empty($venueIds)) {
            return response()->json([]);
        }
        
        $courts = Court::whereIn('venue_id', $venueIds)
            ->with(['venue' => function($q) {
                $q->orderBy('name', 'ASC');
            }])
            ->orderBy('name', 'ASC')
            ->get();
        
        return response()->json($courts);
    }

    public function getCourtsForClubs(): JsonResponse
    {
        $clubIds = request('clubIds');
        if (!is_array($clubIds)) {
            $clubIds = [$clubIds];
        }
        
        $clubVenues = ClubVenues::whereIn('club_id', $clubIds)->get();
        $venueIds = $clubVenues->pluck('venue_id')->toArray();
        
        $courts = Court::whereIn('venue_id', $venueIds)
            ->with(['venue' => function($q) {
                $q->orderBy('name', 'ASC');
            }])
            ->orderBy('name', 'ASC')
            ->get();
        
        return response()->json($courts);
    }

    public function getById(int $id): JsonResponse
    {
        $court = $this->courtService->findOne(['id' => $id]);
        if (!$court) {
            return response()->json(['error' => 'Court not found'], 404);
        }
        return response()->json($court);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->courtService->delete($id);
        return response()->json(['success' => $result]);
    }
}

