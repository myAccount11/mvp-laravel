<?php

namespace App\Services\V5;

use App\Models\V5\ReservationType;
use App\Repositories\V5\ReservationTypeRepository;

class ReservationTypesService
{
    public function __construct(protected ReservationTypeRepository $reservationTypeRepository)
    {
    }

    public function findOne(array $condition): ?ReservationType
    {
        return $this->reservationTypeRepository->findOneBy($condition);
    }

    public function findAll(array $condition = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->reservationTypeRepository->findBy($condition);
    }

    public function create(array $data): ReservationType
    {
        return $this->reservationTypeRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->reservationTypeRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->reservationTypeRepository->delete($id);
    }
}

