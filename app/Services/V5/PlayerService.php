<?php

namespace App\Services\V5;

use App\Models\V5\Player;
use App\Repositories\V5\PlayerRepository;

class PlayerService
{
    public function __construct(protected PlayerRepository $playerRepository)
    {
    }

    public function findAll(string $orderBy = 'id', string $orderDirection = 'asc'): \Illuminate\Database\Eloquent\Collection
    {
        return $this->playerRepository->query()->orderBy($orderBy, $orderDirection)->get();
    }

    public function findOne(array $condition): ?Player
    {
        return $this->playerRepository->findOneBy($condition);
    }

    public function create(array $data): Player
    {
        return $this->playerRepository->create($data);
    }

    public function updateByCondition(array $condition, array $data): int
    {
        $query = $this->playerRepository->query();
        foreach ($condition as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
        return $query->update($data);
    }
}
