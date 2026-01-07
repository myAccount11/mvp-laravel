<?php

namespace App\Services\V5;

use App\Models\V5\TournamentProgram;
use App\Repositories\V5\TournamentProgramRepository;

class TournamentProgramsService
{
    protected TournamentProgramRepository $tournamentProgramRepository;

    public function __construct(TournamentProgramRepository $tournamentProgramRepository)
    {
        $this->tournamentProgramRepository = $tournamentProgramRepository;
    }

    public function findOne(array $condition): ?TournamentProgram
    {
        return $this->tournamentProgramRepository->findOneBy($condition);
    }

    public function findAll(array $condition = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->tournamentProgramRepository->findBy($condition);
    }

    public function create(array $data): TournamentProgram
    {
        return $this->tournamentProgramRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->tournamentProgramRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->tournamentProgramRepository->delete($id);
    }
}

