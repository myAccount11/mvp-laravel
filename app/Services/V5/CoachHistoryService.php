<?php

namespace App\Services\V5;

use App\Models\V5\CoachHistory;
use App\Repositories\V5\CoachHistoryRepository;

class CoachHistoryService
{
    public function __construct(protected CoachHistoryRepository $coachHistoryRepository)
    {
    }

    public function findOne(array $condition): ?CoachHistory
    {
        return $this->coachHistoryRepository->findOneBy($condition);
    }

    public function findAll(string $orderBy = 'id', string $orderDirection = 'asc'): \Illuminate\Database\Eloquent\Collection
    {
        return $this->coachHistoryRepository->query()->orderBy($orderBy, $orderDirection)->get();
    }

    public function create(array $data): CoachHistory
    {
        return $this->coachHistoryRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->coachHistoryRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->coachHistoryRepository->delete($id);
    }
}

