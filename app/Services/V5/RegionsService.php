<?php

namespace App\Services\V5;

use App\Models\V5\Region;
use App\Repositories\V5\RegionRepository;

class RegionsService
{
    protected RegionRepository $regionRepository;

    public function __construct(RegionRepository $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }

    public function findOne(array $condition): ?Region
    {
        return $this->regionRepository->findOneBy($condition);
    }

    public function findAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->regionRepository->all();
    }

    public function create(array $data): Region
    {
        return $this->regionRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->regionRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->regionRepository->delete($id);
    }
}

