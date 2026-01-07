<?php

namespace App\Services\V5;

use App\Models\V5\Reservation;
use App\Repositories\V5\ReservationRepository;
use App\Services\V5\TimeSlotService;
use App\Models\V5\TimeSlot;
use Carbon\Carbon;

class ReservationService
{
    protected ReservationRepository $reservationRepository;
    protected TimeSlotService $timeSlotsService;

    public function __construct(
        ReservationRepository $reservationRepository,
        TimeSlotService $timeSlotsService
    ) {
        $this->reservationRepository = $reservationRepository;
        $this->timeSlotsService = $timeSlotsService;
    }

    public function findAll(array $conditions = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->reservationRepository->query();

        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }

        if (isset($conditions['include'])) {
            $query->with($conditions['include']);
        }

        if (isset($conditions['orderBy'])) {
            $query->orderBy($conditions['orderBy'], $conditions['orderDirection'] ?? 'ASC');
        }

        return $query->get();
    }

    public function findOne(array $condition): ?Reservation
    {
        return $this->reservationRepository->findOneBy($condition);
    }

    public function create(array $data): Reservation
    {
        return $this->reservationRepository->create($data);
    }

    public function bulkCreate(array $data): \Illuminate\Database\Eloquent\Collection
    {
        $reservations = [];
        foreach ($data as $reservationData) {
            $reservations[] = $this->reservationRepository->create($reservationData);
        }
        return collect($reservations);
    }

    public function update(int $id, array $data): bool
    {
        return $this->reservationRepository->update($id, $data);
    }

    public function updateByCondition(array $conditions, array $data): int
    {
        $query = $this->reservationRepository->query();
        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }
        return $query->update($data);
    }

    public function createReservation(array $createReservationDto): Reservation|\Exception
    {
        $time = $this->timeSlotsService->findOne(['id' => $createReservationDto['time_slot_id']]);
        if (!$time) {
            throw new \Exception('Time slot not found');
        }

        $timeStart = Carbon::parse($createReservationDto['start_time'])->format('H:i');
        $timeEnd = Carbon::parse($createReservationDto['end_time'])->format('H:i');

        $check = $this->checkReservation(
            $timeStart,
            $timeEnd,
            $time->court_id,
            $time->date->format('Y-m-d')
        );

        if ($check instanceof \Exception) {
            return $check;
        }

        return $this->create([
            'game_id' => $createReservationDto['game_id'] ?? null,
            'time_slot_id' => $time->id,
            'start_time' => $createReservationDto['start_time'],
            'end_time' => $createReservationDto['end_time'],
            'club_id' => $createReservationDto['club_id'] ?? null,
            'type_id' => $createReservationDto['type_id'] ?? null,
            'text' => $createReservationDto['text'] ?? null,
            'age_group' => $createReservationDto['age_group'] ?? null,
        ]);
    }

    public function checkReservation(
        string $timeStart,
        string $timeEnd,
        int $courtId,
        string $date,
        ?int $timeSlotId = null,
        ?int $reservationId = null
    ): bool|\Exception {
        $query = $this->reservationRepository->query()
            ->where('is_deleted', false)
            ->where(function ($q) use ($timeStart, $timeEnd) {
                $q->where(function ($subQ) use ($timeStart, $timeEnd) {
                    $subQ->where('start_time', '>=', $timeStart)
                         ->where('start_time', '<=', $timeEnd);
                })
                ->orWhere(function ($subQ) use ($timeStart, $timeEnd) {
                    $subQ->where('end_time', '>=', $timeStart)
                         ->where('end_time', '<=', $timeEnd);
                })
                ->orWhere(function ($subQ) use ($timeStart, $timeEnd) {
                    $subQ->where('end_time', '>', $timeStart)
                         ->where('start_time', '<', $timeStart);
                });
            });

        if ($timeSlotId) {
            $query->where('time_slot_id', '!=', $timeSlotId);
        }

        if ($reservationId) {
            $query->where('id', '!=', $reservationId);
        }

        $query->whereHas('timeSlot', function ($q) use ($courtId, $date) {
            $q->where('court_id', $courtId)
              ->where('date', $date);
        });

        $reservation = $query->with('type')->first();

        if ($reservation) {
            $startTimeFormatted = Carbon::parse($reservation->start_time)->format('H:i');
            $endTimeFormatted = Carbon::parse($reservation->end_time)->format('H:i');
            $text = $reservation->text ?: ($reservation->type ? $reservation->type->text : '');
            return new \Exception(
                "The court is reserved for other purposes {$startTimeFormatted} to {$endTimeFormatted} {$text}"
            );
        }

        return false;
    }

    public function updateReservation(int $id, array $updateReservationDto): bool|\Exception
    {
        $time = $this->timeSlotsService->findOne(['id' => $updateReservationDto['time_slot_id']]);
        if (!$time) {
            throw new \Exception('Time slot not found');
        }

        $timeStart = Carbon::parse($updateReservationDto['start_time'])->format('H:i');
        $timeEnd = Carbon::parse($updateReservationDto['end_time'])->format('H:i');

        $check = $this->checkReservation(
            $timeStart,
            $timeEnd,
            $time->court_id,
            $time->date->format('Y-m-d'),
            null,
            $id
        );

        if ($check instanceof \Exception) {
            return $check;
        }

        return $this->update($id, [
            'game_id' => $updateReservationDto['game_id'] ?? null,
            'time_slot_id' => $updateReservationDto['time_slot_id'],
            'start_time' => $updateReservationDto['start_time'],
            'end_time' => $updateReservationDto['end_time'],
            'club_id' => $updateReservationDto['club_id'] ?? null,
            'type_id' => $updateReservationDto['type_id'] ?? null,
            'text' => $updateReservationDto['text'] ?? null,
            'age_group' => $updateReservationDto['age_group'] ?? null,
        ]);
    }
}

