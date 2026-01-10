<?php

namespace App\Services\V5;

use App\Models\V5\TournamentProgramItem;
use App\Repositories\V5\TournamentProgramItemRepository;

class TournamentProgramItemsService
{
    protected TournamentProgramItemRepository $tournamentProgramItemRepository;

    public function __construct(TournamentProgramItemRepository $tournamentProgramItemRepository)
    {
        $this->tournamentProgramItemRepository = $tournamentProgramItemRepository;
    }

    public function findOne(array $condition): ?TournamentProgramItem
    {
        return $this->tournamentProgramItemRepository->findOneBy($condition);
    }

    public function findAll(array $condition = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->tournamentProgramItemRepository->findBy($condition);
    }

    public function create(array $data): TournamentProgramItem
    {
        return $this->tournamentProgramItemRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->tournamentProgramItemRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->tournamentProgramItemRepository->delete($id);
    }

    public function deleteByCondition(array $condition): bool
    {
        $query = $this->tournamentProgramItemRepository->query();
        
        if (isset($condition['where'])) {
            foreach ($condition['where'] as $key => $value) {
                $query->where($key, $value);
            }
        } else {
            foreach ($condition as $key => $value) {
                $query->where($key, $value);
            }
        }
        
        return $query->delete() > 0;
    }
}


