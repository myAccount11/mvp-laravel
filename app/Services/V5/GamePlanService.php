<?php

namespace App\Services\V5;

use App\Models\V5\GamePlan;
use App\Repositories\V5\GamePlanRepository;

class GamePlanService
{
    protected GamePlanRepository $gamePlanRepository;

    public function __construct(GamePlanRepository $gamePlanRepository)
    {
        $this->gamePlanRepository = $gamePlanRepository;
    }

    public function findAll(string $orderBy = 'id', string $orderDirection = 'asc'): \Illuminate\Database\Eloquent\Collection
    {
        return $this->gamePlanRepository->query()->orderBy($orderBy, $orderDirection)->get();
    }

    public function findOne(array $condition): ?GamePlan
    {
        return $this->gamePlanRepository->findOneBy($condition);
    }

    public function create(array $data): GamePlan
    {
        return $this->gamePlanRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->gamePlanRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->gamePlanRepository->delete($id);
    }
}

