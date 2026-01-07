<?php

namespace App\Services\V5;

use App\Models\V5\TimeSlot;
use App\Repositories\V5\TimeSlotRepository;
use App\Services\V5\SeasonSportService;
use App\Models\V5\Season;
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
            $query->where($conditions['where']);
        }

        return $query->get();
    }

    public function findOne(array $condition): ?TimeSlot
    {
        return $this->timeSlotRepository->findOneBy($condition);
    }

    public function create(array $data): TimeSlot
    {
        return $this->timeSlotRepository->create($data);
    }

    public function createTimeSlot(array $createTimeSlotDto): TimeSlot|\Illuminate\Support\Collection
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

        $bulkData = [];
        $seasonSport = $this->seasonSportService->findOne(['id' => $data['season_sport_id']]);

        if (!$seasonSport) {
            throw new \Exception('Season sport not found');
        }

        $season = $seasonSport->season;
        if (!$season) {
            throw new \Exception('Season not found');
        }

        $seasonNameParts = explode('/', $season->name);
        $seasonEnd = Carbon::parse($seasonNameParts[1] . '-05-31');

        $currentDate = Carbon::parse($date);
        while ($currentDate->isBefore($seasonEnd)) {
            $bulkData[] = array_merge($data, [
                'date' => $currentDate->format('Y-m-d'),
                'expiration' => $expiration,
            ]);

            if ($useDifferent) {
                $expiration = Carbon::parse($expiration)->addDays(7)->format('Y-m-d');
            }
            $currentDate->addDays(7);
        }

        $this->timeSlotRepository->query()->insert($bulkData);
        return collect($bulkData);
    }
}

