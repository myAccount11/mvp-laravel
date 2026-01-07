<?php

namespace App\Services\V5;

use App\Models\V5\UserSeasonSport;
use App\Repositories\V5\UserSeasonSportRepository;

class UserSeasonSportsService
{
    protected UserSeasonSportRepository $userSeasonSportRepository;

    public function __construct(UserSeasonSportRepository $userSeasonSportRepository)
    {
        $this->userSeasonSportRepository = $userSeasonSportRepository;
    }

    public function findOne(array $condition): ?UserSeasonSport
    {
        return $this->userSeasonSportRepository->findOneBy($condition);
    }

    public function findAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->userSeasonSportRepository->all();
    }

    public function create(array $data): UserSeasonSport
    {
        return $this->userSeasonSportRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->userSeasonSportRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->userSeasonSportRepository->delete($id);
    }
}

