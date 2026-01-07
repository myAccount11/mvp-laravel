<?php

namespace App\Services\V5;

use App\Models\V5\TournamentGroup;
use App\Repositories\V5\TournamentGroupRepository;

class TournamentGroupService
{
    protected TournamentGroupRepository $tournamentGroupRepository;

    public function __construct(TournamentGroupRepository $tournamentGroupRepository)
    {
        $this->tournamentGroupRepository = $tournamentGroupRepository;
    }

    public function findOne(array $condition): ?TournamentGroup
    {
        return $this->tournamentGroupRepository->findOneBy($condition);
    }

    public function findAll(array $conditions = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->tournamentGroupRepository->query();

        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }

        if (isset($conditions['include'])) {
            $query->with($conditions['include']);
        }

        return $query->get();
    }

    public function create(array $data): TournamentGroup
    {
        return $this->tournamentGroupRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->tournamentGroupRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->tournamentGroupRepository->delete($id);
    }
}

