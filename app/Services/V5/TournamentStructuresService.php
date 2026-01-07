<?php

namespace App\Services\V5;

use App\Models\V5\TournamentStructure;
use App\Repositories\V5\TournamentStructureRepository;

class TournamentStructuresService
{
    protected TournamentStructureRepository $tournamentStructureRepository;

    public function __construct(TournamentStructureRepository $tournamentStructureRepository)
    {
        $this->tournamentStructureRepository = $tournamentStructureRepository;
    }

    public function findOne(array $condition): ?TournamentStructure
    {
        return $this->tournamentStructureRepository->findOneBy($condition);
    }

    public function findAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->tournamentStructureRepository->all();
    }

    public function create(array $data): TournamentStructure
    {
        return $this->tournamentStructureRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->tournamentStructureRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->tournamentStructureRepository->delete($id);
    }
}

