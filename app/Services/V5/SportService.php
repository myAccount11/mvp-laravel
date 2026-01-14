<?php

namespace App\Services\V5;

use App\Models\V5\Sport;
use App\Repositories\V5\SportRepository;

class SportService
{
    public function __construct(protected SportRepository $sportRepository)
    {
    }

    public function findAll(string $orderBy = 'id', string $orderDirection = 'asc'): \Illuminate\Database\Eloquent\Collection
    {
        return $this->sportRepository->query()->orderBy($orderBy, $orderDirection)->get();
    }

    public function findOne(array $condition): ?Sport
    {
        return $this->sportRepository->findOneBy($condition);
    }

    public function create(array $data): Sport
    {
        return $this->sportRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->sportRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->sportRepository->delete($id);
    }
}

