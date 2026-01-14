<?php

namespace App\Services\V5;

use App\Models\V5\Conflict;
use App\Repositories\V5\ConflictRepository;
use Illuminate\Database\Eloquent\Collection;

class ConflictService
{
    public function __construct(protected ConflictRepository $conflictRepository)
    {
    }

    public function findAll(array $conditions = []): Collection
    {
        $query = $this->conflictRepository->query();

        if (isset($conditions['where'])) {
            $whereConditions = $conditions['where'];
            if (is_callable($whereConditions)) {
                // Single closure
                $query->where($whereConditions);
            } elseif (is_array($whereConditions)) {
                // Array of conditions - handle closures and key-value pairs
                foreach ($whereConditions as $key => $value) {
                    if (is_callable($value)) {
                        // Closure/function - pass directly to where()
                        $query->where($value);
                    } elseif (is_array($value)) {
                        // Array condition like ['column', 'operator', 'value']
                        if (count($value) === 3) {
                            $query->where($value[0], $value[1], $value[2]);
                        } elseif (count($value) === 2) {
                            $query->where($value[0], $value[1]);
                        }
                    } else {
                        // Simple key-value pair
                        $query->where($key, $value);
                    }
                }
            }
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
            $whereConditions = $conditions['where'];
            if (is_callable($whereConditions)) {
                $query->where($whereConditions);
            } elseif (is_array($whereConditions)) {
                foreach ($whereConditions as $key => $value) {
                    if (is_callable($value)) {
                        $query->where($value);
                    } elseif (is_array($value)) {
                        if (count($value) === 3) {
                            $query->where($value[0], $value[1], $value[2]);
                        } elseif (count($value) === 2) {
                            $query->where($value[0], $value[1]);
                        }
                    } else {
                        $query->where($key, $value);
                    }
                }
            }
        }
        return $query->delete();
    }
}

