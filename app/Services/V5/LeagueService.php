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

