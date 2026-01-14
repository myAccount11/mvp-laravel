<?php

namespace App\Services\V5;

use App\Models\V5\Organizer;
use App\Repositories\V5\OrganizerRepository;

class OrganizerService
{
    public function __construct(protected OrganizerRepository $organizerRepository)
    {
    }

    public function findAll(string $orderBy = 'id', string $orderDirection = 'asc'): \Illuminate\Database\Eloquent\Collection
    {
        return $this->organizerRepository->query()->orderBy($orderBy, $orderDirection)->get();
    }

    public function findOne(array $condition): ?Organizer
    {
        return $this->organizerRepository->findOneBy($condition);
    }

    public function create(array $data): Organizer
    {
        return $this->organizerRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->organizerRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->organizerRepository->delete($id);
    }
}

