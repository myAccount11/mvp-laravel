<?php

namespace App\Services\V5;

use App\Models\V5\TournamentType;
use App\Repositories\V5\TournamentTypeRepository;

class TournamentTypesService
{
    protected TournamentTypeRepository $tournamentTypeRepository;

    public function __construct(TournamentTypeRepository $tournamentTypeRepository)
    {
        $this->tournamentTypeRepository = $tournamentTypeRepository;
    }

    public function findOne(array $condition): ?TournamentType
    {
        return $this->tournamentTypeRepository->findOneBy($condition);
    }

    public function findAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->tournamentTypeRepository->all();
    }

    public function create(array $data): TournamentType
    {
        return $this->tournamentTypeRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->tournamentTypeRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->tournamentTypeRepository->delete($id);
    }
}

