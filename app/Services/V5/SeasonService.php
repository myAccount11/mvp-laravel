<?php

namespace App\Services\V5;

use App\Models\V5\Season;
use App\Repositories\V5\SeasonRepository;

class SeasonService
{
    public function __construct(protected SeasonRepository $seasonRepository)
    {
    }

    public function findAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->seasonRepository->all();
    }

    public function findOne(array $condition): ?Season
    {
        return $this->seasonRepository->findOneBy($condition);
    }

    public function create(array $data): Season
    {
        return $this->seasonRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->seasonRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->seasonRepository->delete($id);
    }
}

