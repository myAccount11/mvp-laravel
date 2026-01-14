<?php

namespace App\Services\V5;

use App\Models\V5\GamePenalty;
use App\Repositories\V5\GamePenaltyRepository;

class GamePenaltiesService
{
    public function __construct(protected GamePenaltyRepository $gamePenaltyRepository)
    {
    }

    public function findOne(array $condition): ?GamePenalty
    {
        return $this->gamePenaltyRepository->findOneBy($condition);
    }

    public function create(array $data): GamePenalty
    {
        return $this->gamePenaltyRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->gamePenaltyRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->gamePenaltyRepository->delete($id);
    }
}

