<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateTimeSlotRequest;
use App\Http\Requests\V5\UpdateTimeSlotRequest;
use App\Services\V5\TimeSlotService;
use App\Services\V5\VenueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    /**
     * Get all time slots with filtering, pagination, and search
     */
    public function getAll(Request $request): JsonResponse
    {
        $user = Auth::user();
        $queryParams = $request->all();

        $orderBy = $queryParams['order_by'] ?? 'id';
        $orderDirection = $queryParams['order_direction'] ?? 'ASC';
        $page = (int)($queryParams['page'] ?? 1);
        $limit = (int)($queryParams['limit'] ?? 20);
        $period = $queryParams['period'] ?? null;
        $searchTerm = $queryParams['search_term'] ?? null;
        $venueId = $queryParams['venue_id'] ?? null;

        // Check if user is admin (SUPER_ADMIN or ASSOCIATION_ADMIN)
        $isAdmin = $user->roles->contains(function ($role) {
            return in_array($role->description, ['Super Admin', 'ASSOCIATION_ADMIN']);
        });

        $whereConditions = [];

        // Filter by user's clubs (if not admin)
        if ($isAdmin) {
            // Admin sees time slots where club_id is null (federation time slots)
            $whereConditions[] = function ($q) {
                $q->whereNull('club_id');
            };
        } else {
            // Non-admin users see only their club's time slots
            $clubIds = $user->userRoles()
                ->where('role_id', 1) // CLUB_MANAGER role
                ->whereNotNull('club_id')
                ->where('user_role_approved_by_user_id', '>', 0)
                ->pluck('club_id')
                ->unique()
                ->toArray();

            if (!empty($clubIds)) {
                $whereConditions[] = function ($q) use ($clubIds) {
                    $q->whereIn('club_id', $clubIds);
                };
            } else {
                // User has no clubs, return empty result
                return response()->json([
                    'rows'  => [],
                    'count' => 0,
                ]);
            }
        }

        // Filter by venue (if provided)
        if ($venueId) {
            $venue = $this->venueService->findOne(['id' => $venueId], ['courts']);

            if ($venue && $venue->courts->isNotEmpty()) {
                $courtIds = $venue->courts->pluck('id')->toArray();
                $whereConditions[] = function ($q) use ($courtIds) {
                    $q->whereIn('court_id', $courtIds);
                };
            } else {
                // Venue not found or has no courts, return empty result
                return response()->json([
                    'rows'  => [],
                    'count' => 0,
                ]);
            }
        }

        // Filter by date period (if provided)
        if ($period && is_array($period) && count($period) === 2) {
            $whereConditions[] = ['date', '>=', $period[0]];
            $whereConditions[] = ['date', '<=', $period[1]];
        }

        // Build order array
        $order = [];
        if ($orderBy === 'court') {
            // Order by court.venue.name
            $order[] = ['court', 'venue', 'name', $orderDirection];
        } elseif ($orderBy === 'club') {
            // Order by club.name
            $order[] = ['club', 'name', $orderDirection];
        } else {
            // Default order
            $order[] = [$orderBy, $orderDirection];
        }

        // Always order by reservations.start_time asc
        $order[] = ['reservations', 'start_time', 'asc'];

        // Build include array with optional reservation search
        $includes = [
            'court.venue',
            'club',
        ];

        $reservationWhere = function ($q) use ($searchTerm) {
            $q->where('is_deleted', false);
            if ($searchTerm) {
                $q->where('text', 'ILIKE', "%{$searchTerm}%"); // PostgreSQL case-insensitive like
            }
        };

        // If searchTerm is provided, make reservation relation required (inner join)
        // Otherwise, it's optional (left join)
        if ($searchTerm) {
            $includes['reservations'] = $reservationWhere;
            // When searching, we need whereHas to filter main query
            $whereConditions[] = function ($q) use ($reservationWhere) {
                $q->whereHas('reservations', $reservationWhere);
            };
        } else {
            $includes['reservations'] = $reservationWhere;
        }

        $result = $this->timeSlotService->findAndCountAll([
            'where'   => $whereConditions,
            'include' => $includes,
            'order'   => $order,
            'limit'   => $limit,
            'offset'  => ($page - 1) * $limit,
        ]);

        return response()->json($result);
    }

    /**
     * Create a new time slot
     */
    public function create(CreateTimeSlotRequest $request): JsonResponse
    {
        try {
            $result = $this->timeSlotService->createTimeSlot($request->validated());

            if ($result instanceof \Illuminate\Support\Collection) {
                // Bulk create (weekly)
                return response()->json([
                    'message' => 'Time slots created successfully',
                    'count'   => $result->count(),
                    'data'    => $result,
                ], 201);
            }

            return response()->json($result, 201);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to create time slot',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get time slot by ID
     */
    public function getById(int $id): JsonResponse
    {
        $timeSlot = $this->timeSlotService->findOne([
            'where'   => ['id' => $id],
            'include' => ['court.venue'],
        ]);

        if (!$timeSlot) {
            return response()->json(['error' => 'Time slot not found'], 404);
        }

        return response()->json($timeSlot);
    }

    /**
     * Update time slot
     */
    public function update(int $id, UpdateTimeSlotRequest $request): JsonResponse
    {
        try {
            $result = $this->timeSlotService->update($id, $request->validated());

            if (!$result) {
                return response()->json(['error' => 'Time slot not found'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Time slot updated successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to update time slot',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete time slot
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->timeSlotService->delete($id);

            if (!$result) {
                return response()->json(['error' => 'Time slot not found'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Time slot deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to delete time slot',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
