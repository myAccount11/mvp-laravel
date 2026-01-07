<?php

namespace App\Services\V5;

use App\Models\V5\Conflict;
use App\Repositories\V5\ConflictRepository;

class ConflictService
{
    protected ConflictRepository $conflictRepository;

    public function __construct(ConflictRepository $conflictRepository)
    {
        $this->conflictRepository = $conflictRepository;
    }

    public function findAll(array $conditions = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->conflictRepository->query();

        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }

        if (isset($conditions['attributes'])) {
            $query->select($conditions['attributes']);
        }

        return $query->get();
    }

    public function findOne(array $condition): ?Conflict
    {
        return $this->conflictRepository->findOneBy($condition);
    }

    public function create(array $data): Conflict
    {
        return $this->conflictRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->conflictRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->conflictRepository->delete($id);
    }

    public function deleteByCondition(array $conditions): int
    {
        $query = $this->conflictRepository->query();
        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }
        return $query->delete();
    }
}

