<?php

namespace App\Services\V5;

use App\Repositories\V5\TimeSlotRepository;
use App\Services\V5\SeasonSportService;
use Carbon\Carbon;

class TimeSlotService
{
    protected TimeSlotRepository $timeSlotRepository;
    protected SeasonSportService $seasonSportService;

    public function __construct(
        TimeSlotRepository $timeSlotRepository,
        SeasonSportService $seasonSportService
    ) {
        $this->timeSlotRepository = $timeSlotRepository;
        $this->seasonSportService = $seasonSportService;
    }

    public function findAll(array $conditions = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->timeSlotRepository->query();

        if (isset($conditions['where'])) {
            $whereConditions = $conditions['where'];
            if (is_callable($whereConditions)) {
                $query->where($whereConditions);
            } elseif (is_array($whereConditions)) {
                foreach ($whereConditions as $key => $value) {
                    if (is_callable($value)) {
                        $query->where($value);
                    } elseif (is_array($value)) {
                        if (count($value) === 3) {
                            $query->where($value[0], $value[1], $value[2]);
                        } elseif (count($value) === 2) {
                            $query->where($value[0], $value[1]);
                        }
                    } else {
                        $query->where($key, $value);
                    }
                }
            }
        }

        if (isset($conditions['include'])) {
            $query->with($conditions['include']);
        }

        return $query->get();
    }

    public function findAndCountAll(array $conditions = []): array
    {
        $query = $this->timeSlotRepository->query();

        // Handle where conditions
        if (isset($conditions['where'])) {
            $whereConditions = $conditions['where'];
            if (is_callable($whereConditions)) {
                $query->where($whereConditions);
            } elseif (is_array($whereConditions)) {
                foreach ($whereConditions as $key => $value) {
                    if (is_callable($value)) {
                        $query->where($value);
                    } elseif (is_array($value)) {
                        if (count($value) === 3) {
                            $query->where($value[0], $value[1], $value[2]);
                        } elseif (count($value) === 2) {
                            $query->where($value[0], $value[1]);
                        }
                    } else {
                        $query->where($key, $value);
                    }
                }
            }
        }

        // Handle whereHas conditions for included relations (affects count)
        if (isset($conditions['include']) && is_array($conditions['include'])) {
            $query->with($conditions['include']);
        }

        // Get count before any joins or pagination
        $count = (clone $query)->count();

        // Determine if we need joins for ordering
        $needsJoinForCourtVenueOrder = false;
        $needsJoinForClubOrder = false;
        $orderItems = $conditions['order'] ?? [];

        foreach ($orderItems as $orderItem) {
            if (is_array($orderItem) && count($orderItem) >= 4 &&
                $orderItem[0] === 'court' && $orderItem[1] === 'venue') {
                $needsJoinForCourtVenueOrder = true;
            } elseif (is_array($orderItem) && count($orderItem) >= 2 &&
                      $orderItem[0] === 'club' && ($orderItem[1] ?? null) === 'name') {
                $needsJoinForClubOrder = true;
            }
        }

        // Apply joins for ordering if needed
        if ($needsJoinForCourtVenueOrder) {
            $query->join('courts', 'time_slots.court_id', '=', 'courts.id')
                  ->join('venues', 'courts.venue_id', '=', 'venues.id')
                  ->select('time_slots.*');
        }

        if ($needsJoinForClubOrder && !$needsJoinForCourtVenueOrder) {
            $query->leftJoin('clubs', 'time_slots.club_id', '=', 'clubs.id')
                  ->select('time_slots.*');
        } elseif ($needsJoinForClubOrder && $needsJoinForCourtVenueOrder) {
            $query->leftJoin('clubs', 'time_slots.club_id', '=', 'clubs.id');
        }

        // Apply ordering (excluding reservations which will be sorted in memory)
        foreach ($orderItems as $orderItem) {
            if (is_array($orderItem)) {
                if (count($orderItem) >= 4 && $orderItem[0] === 'court' && $orderItem[1] === 'venue') {
                    $query->orderBy('venues.' . $orderItem[2], $orderItem[3] ?? 'ASC');
                } elseif (count($orderItem) >= 2 && $orderItem[0] === 'club' && ($orderItem[1] ?? null) === 'name') {
                    $query->orderBy('clubs.name', $orderItem[2] ?? 'ASC');
                } elseif (count($orderItem) >= 2 && $orderItem[0] !== 'reservations') {
                    $query->orderBy($orderItem[0], $orderItem[1] ?? 'ASC');
                }
            } elseif (is_string($orderItem)) {
                $query->orderBy($orderItem, 'ASC');
            }
        }

        // Apply pagination
        if (isset($conditions['limit'])) {
            $query->limit($conditions['limit']);
        }
        if (isset($conditions['offset'])) {
            $query->offset($conditions['offset']);
        }

        // Get IDs first if we used joins (to handle potential duplicates and maintain order)
        $orderedIds = null;
        if ($needsJoinForCourtVenueOrder || $needsJoinForClubOrder) {
            $orderedIds = (clone $query)->distinct()->pluck('time_slots.id')->toArray();
            // Rebuild query without joins for clean data fetch
            $query = $this->timeSlotRepository->query()->whereIn('id', $orderedIds);
        }

        // Handle include relations for eager loading
        if (isset($conditions['include'])) {
            $includes = [];
            foreach ($conditions['include'] as $key => $value) {
                if (is_string($key)) {
                    $includes[$key] = $value;
                } else {
                    if (is_string($value)) {
                        $includes[] = $value;
                    }
                }
            }
            if (!empty($includes)) {
                $query->with($includes);
            }
        }

        $rows = $query->get();

        // If we used joins, maintain the order from the join query
        if ($orderedIds !== null && !empty($orderedIds)) {
            $rowsById = $rows->keyBy('id');
            $rows = collect($orderedIds)->map(function ($id) use ($rowsById) {
                return $rowsById->get($id);
            })->filter();
        }

        // Sort by related columns in memory if needed (for nested relations or reservations)
        foreach ($orderItems as $orderItem) {
            if (is_array($orderItem)) {
                if (count($orderItem) >= 4 && $orderItem[0] === 'court' && $orderItem[1] === 'venue' && !$needsJoinForCourtVenueOrder) {
                    // Sort by court.venue.name (if not already sorted via join)
                    $direction = strtolower($orderItem[3] ?? 'asc');
                    $rows = $rows->sortBy(function ($timeSlot) {
                        return $timeSlot->court?->venue?->name ?? '';
                    }, SORT_REGULAR, $direction === 'desc');
                } elseif (count($orderItem) >= 2 && $orderItem[0] === 'club' && ($orderItem[1] ?? null) === 'name' && !$needsJoinForClubOrder) {
                    // Sort by club.name (if not already sorted via join)
                    $direction = strtolower($orderItem[2] ?? 'asc');
                    $rows = $rows->sortBy(function ($timeSlot) {
                        return $timeSlot->club?->name ?? '';
                    }, SORT_REGULAR, $direction === 'desc');
                } elseif (isset($orderItem[0]) && $orderItem[0] === 'reservations' &&
                          isset($orderItem[1]) && $orderItem[1] === 'start_time') {
                    // Always sort reservations in memory
                    $direction = strtolower($orderItem[2] ?? 'asc');
                    $rows = $rows->sortBy(function ($timeSlot) {
                        $firstReservation = $timeSlot->reservations->first();
                        return $firstReservation ? $firstReservation->start_time : '';
                    }, SORT_REGULAR, $direction === 'desc');
                }
            }
        }

        return [
            'rows'  => $rows->values(),
            'count' => $count,
        ];
    }

    public function findOne(array $condition): ?\App\Models\V5\TimeSlot
    {
        $query = $this->timeSlotRepository->query();

        // Handle where conditions
        if (isset($condition['where'])) {
            $whereConditions = $condition['where'];
            if (is_array($whereConditions)) {
                foreach ($whereConditions as $key => $value) {
                    if (is_callable($value)) {
                        $query->where($value);
                    } elseif (is_array($value)) {
                        if (count($value) === 3) {
                            $query->where($value[0], $value[1], $value[2]);
                        } elseif (count($value) === 2) {
                            $query->where($value[0], $value[1]);
                        }
                    } else {
                        $query->where($key, $value);
                    }
                }
            }
        } else {
            // Backward compatibility: if no 'where' key, treat condition as where clauses
            foreach ($condition as $key => $value) {
                if ($key !== 'include') {
                    $query->where($key, $value);
                }
            }
        }

        // Handle include relations
        if (isset($condition['include'])) {
            $query->with($condition['include']);
        }

        return $query->first();
    }

    public function create(array $data): \App\Models\V5\TimeSlot
    {
        return $this->timeSlotRepository->create($data);
    }

    public function createTimeSlot(array $createTimeSlotDto): \App\Models\V5\TimeSlot|\Illuminate\Support\Collection
    {
        $createWeekly = $createTimeSlotDto['create_weekly'] ?? false;
        $useDifferent = $createTimeSlotDto['use_different'] ?? false;

        $data = $createTimeSlotDto;
        unset($data['create_weekly'], $data['use_different']);

        if (!$createWeekly) {
            return $this->create($data);
        }

        $date = Carbon::parse($data['date'])->format('Y-m-d');
        $expiration = Carbon::parse($data['expiration'])->format('Y-m-d');
        $originalExpiration = $expiration;

        $bulkData = [];
        $seasonSport = $this->seasonSportService->findOne([
            'where' => ['id' => $data['season_sport_id']],
            'include' => ['season'],
        ]);

        if (!$seasonSport || !$seasonSport->season) {
            throw new \Exception('Season sport or season not found');
        }

        $season = $seasonSport->season;
        $seasonNameParts = explode('/', $season->name);
        if (count($seasonNameParts) < 2) {
            throw new \Exception('Invalid season name format');
        }

        $seasonEnd = Carbon::parse($seasonNameParts[1] . '-05-31');
        $currentDate = Carbon::parse($date);

        while ($currentDate->isBefore($seasonEnd)) {
            $bulkData[] = array_merge($data, [
                'date'       => $currentDate->format('Y-m-d'),
                'expiration' => $expiration,
            ]);

            if ($useDifferent) {
                $expiration = Carbon::parse($expiration)->addDays(7)->format('Y-m-d');
            }
            $currentDate->addDays(7);
        }

        if (empty($bulkData)) {
            throw new \Exception('No time slots to create');
        }

        $this->timeSlotRepository->query()->insert($bulkData);
        return collect($bulkData);
    }

    public function update(int $id, array $data): bool
    {
        return $this->timeSlotRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->timeSlotRepository->delete($id);
    }
}
