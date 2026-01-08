<?php

namespace App\Services\V5;

use App\Models\V5\Round;
use App\Repositories\V5\RoundRepository;
use App\Services\V5\TournamentService;
use Carbon\Carbon;

class RoundService
{
    protected RoundRepository $roundRepository;
    protected TournamentService $tournamentService;

    public function __construct(RoundRepository $roundRepository, TournamentService $tournamentService)
    {
        $this->roundRepository = $roundRepository;
        $this->tournamentService = $tournamentService;
    }

    public function findAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->roundRepository->all();
    }

    public function findOne(array $condition): ?Round
    {
        return $this->roundRepository->findOneBy($condition);
    }

    public function create(array $data): Round
    {
        return $this->roundRepository->create($data);
    }

    public function createMany(array $data): \Illuminate\Support\Collection
    {
        // Convert 0 to null for tournament_id (foreign key constraint)
        $tournamentId = ($data['tournament_id'] === 0 || $data['tournament_id'] === '0' || empty($data['tournament_id'])) 
            ? null 
            : $data['tournament_id'];
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        // Adjust start date to next Sunday
        $startDate = $startDate->copy()->addDays(7 - $startDate->dayOfWeek);

        $insertData = [];
        $index = 0;

        while ($index < 52 && $startDate->copy()->addDays(6)->isBefore($endDate)) {
            $insertData[] = [
                'number' => $index + 1,
                'tournament_id' => $tournamentId,
                'from_date' => $startDate->format('Y-m-d'),
                'to_date' => $startDate->copy()->addDays(6)->format('Y-m-d'),
                'week' => $startDate->week,
                'year' => $startDate->copy()->addDays(6)->year,
                'type' => 0,
                'force_cross' => false,
                'deleted' => false,
            ];

            $startDate->addDays(7);
            $index++;
        }

        if (!empty($insertData)) {
            $this->roundRepository->query()->insert($insertData);
        }
        return collect($insertData);
    }

    public function recreate(array $data): \Illuminate\Support\Collection
    {
        $existingIds = collect($data)->filter(fn($round) => !empty($round['id']))->pluck('id')->toArray();

        // Delete existing rounds
        if (!empty($existingIds)) {
            $this->roundRepository->query()->whereIn('id', $existingIds)->delete();
        }

        // Create new rounds
        $newRounds = [];
        foreach ($data as $round) {
            unset($round['id']);
            $newRounds[] = $this->roundRepository->create($round);
        }

        return collect($newRounds);
    }

    public function updateMany(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $roundIds = collect($data)->pluck('id')->toArray();
        $tournamentId = $data[0]['tournament_id'] ?? null;
        
        // Convert 0 to null for tournament_id (foreign key constraint)
        if ($tournamentId === 0 || $tournamentId === '0') {
            $tournamentId = null;
        }

        if ($tournamentId !== null) {
            $this->roundRepository->query()->whereIn('id', $roundIds)
                ->update(['tournament_id' => $tournamentId]);
        }

        $deletedRounds = collect($data)->filter(fn($round) => !empty($round['deleted']))->pluck('id')->toArray();

        if (!empty($deletedRounds)) {
            $this->roundRepository->query()->whereIn('id', $deletedRounds)
                ->update(['number' => 0, 'deleted' => true]);
        }

        $activeRounds = collect($data)->filter(fn($round) => empty($round['deleted']));

        foreach ($activeRounds as $round) {
            $this->roundRepository->update($round['id'], ['number' => $round['number']]);
        }

        return true;
    }

    public function update(int $id, array $data): bool
    {
        return $this->roundRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->roundRepository->delete($id);
    }

    public function destroyByCondition(array $conditions): int
    {
        $query = $this->roundRepository->query();
        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }
        return $query->delete();
    }
}

