<?php

namespace App\Services\V5;

use App\Models\V5\League;
use App\Repositories\V5\LeagueRepository;

class LeagueService
{
    protected LeagueRepository $leagueRepository;

    public function __construct(LeagueRepository $leagueRepository)
    {
        $this->leagueRepository = $leagueRepository;
    }

    public function findAll(array $conditions = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->leagueRepository->query();

        if (isset($conditions['where']) && is_array($conditions['where'])) {
            foreach ($conditions['where'] as $whereCondition) {
                if (is_callable($whereCondition)) {
                    // Handle closures
                    $query->where($whereCondition);
                } elseif (is_array($whereCondition)) {
                    // Handle array conditions like ['column', 'operator', 'value']
                    if (count($whereCondition) === 3) {
                        $query->where($whereCondition[0], $whereCondition[1], $whereCondition[2]);
                    } elseif (count($whereCondition) === 2) {
                        $query->where($whereCondition[0], $whereCondition[1]);
                    }
                }
            }
        }

        if (isset($conditions['include'])) {
            $query->with($conditions['include']);
        }

        if (isset($conditions['orderBy'])) {
            $query->orderBy($conditions['orderBy'], $conditions['orderDirection'] ?? 'ASC');
        }

        return $query->get();
    }

    public function findOne(array $condition): ?League
    {
        return $this->leagueRepository->findOneBy($condition);
    }

    public function create(array $data): League
    {
        return $this->leagueRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->leagueRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->leagueRepository->delete($id);
    }
}

