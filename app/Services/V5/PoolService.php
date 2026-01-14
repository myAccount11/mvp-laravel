<?php

namespace App\Services\V5;

use App\Models\V5\Pool;
use App\Repositories\V5\PoolRepository;
use Illuminate\Database\Eloquent\Collection;

class PoolService
{
    public function __construct(protected PoolRepository $poolRepository)
    {
    }

    public function findAll(array $conditions = []): Collection
    {
        $query = $this->poolRepository->query();

        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }

        return $query->get();
    }

    public function findOne(array $condition): ?Pool
    {
        return $this->poolRepository->findOneBy($condition);
    }

    public function create(array $data): Pool
    {
        return $this->poolRepository->create($data);
    }

    public function createMany(array $data): Collection
    {
        $pools = [];
        foreach ($data as $poolData) {
            unset($poolData['id']); // Remove id if present
            $pools[] = $this->poolRepository->create($poolData);
        }
        return new Collection($pools);
    }

    public function createOrUpdate(int $tournamentId, array $data): Collection
    {
        $existingPoolIds = collect($data)->filter(fn($pool) => !empty($pool['id']))->pluck('id')->toArray();

        $newData = collect($data)->filter(fn($pool) => empty($pool['id']))->map(function ($pool) {
            unset($pool['id']);
            return $pool;
        })->toArray();

        // Delete pools not in the new list
        if (!empty($existingPoolIds)) {
            $this->poolRepository->query()->where('tournament_id', $tournamentId)
                ->whereNotIn('id', $existingPoolIds)
                ->delete();
        } else {
            // If no existing pools, delete all pools for this tournament
            $this->poolRepository->query()->where('tournament_id', $tournamentId)->delete();
        }

        // Create new pools
        if (!empty($newData)) {
            foreach ($newData as $poolData) {
                $poolData['tournament_id'] = $tournamentId;
                $this->poolRepository->create($poolData);
            }
        }

        // Update existing pools
        foreach ($data as $pool) {
            if (!empty($pool['id'])) {
                $id = $pool['id'];
                unset($pool['id']);
                $pool['tournament_id'] = $tournamentId;
                $this->poolRepository->update($id, $pool);
            }
        }

        return $this->findAll(['where' => ['tournament_id' => $tournamentId]]);
    }

    public function update(int $id, array $data): bool
    {
        return $this->poolRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->poolRepository->delete($id);
    }

    public function destroyByCondition(array $conditions): int
    {
        $query = $this->poolRepository->query();
        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }
        return $query->delete();
    }
}

