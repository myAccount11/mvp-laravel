<?php

namespace App\Services\V5;

use App\Models\V5\TournamentConfig;
use App\Repositories\V5\TournamentConfigRepository;

class TournamentConfigsService
{
    protected TournamentConfigRepository $tournamentConfigRepository;

    public function __construct(TournamentConfigRepository $tournamentConfigRepository)
    {
        $this->tournamentConfigRepository = $tournamentConfigRepository;
    }

    public function findOne(array $condition): ?TournamentConfig
    {
        return $this->tournamentConfigRepository->findOneBy($condition);
    }

    public function findAll(array $condition = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->tournamentConfigRepository->findBy($condition);
    }

    public function findAndCountAll(array $condition): array
    {
        return $this->tournamentConfigRepository->findAndCountAll($condition);
    }

    public function create(array $data): TournamentConfig
    {
        return $this->tournamentConfigRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->tournamentConfigRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->tournamentConfigRepository->delete($id);
    }
}

