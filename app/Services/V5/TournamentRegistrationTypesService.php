<?php

namespace App\Services\V5;

use App\Models\V5\TournamentRegistrationType;
use App\Repositories\V5\TournamentRegistrationTypeRepository;

class TournamentRegistrationTypesService
{
    public function __construct(protected TournamentRegistrationTypeRepository $tournamentRegistrationTypeRepository)
    {
    }

    public function findOne(array $condition): ?TournamentRegistrationType
    {
        return $this->tournamentRegistrationTypeRepository->findOneBy($condition);
    }

    public function findAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->tournamentRegistrationTypeRepository->all();
    }

    public function create(array $data): TournamentRegistrationType
    {
        return $this->tournamentRegistrationTypeRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->tournamentRegistrationTypeRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->tournamentRegistrationTypeRepository->delete($id);
    }
}

