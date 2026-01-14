<?php

namespace App\Services\V5;

use App\Models\V5\SeasonSport;
use App\Repositories\V5\SeasonSportRepository;
use Illuminate\Database\Eloquent\Collection;

class SeasonSportService
{
    public function __construct(protected SeasonSportRepository $seasonSportRepository)
    {
    }

    public function findAll($include = null): Collection
    {
        $query = $this->seasonSportRepository->query();

        if ($include) {
            // Parse include parameter (can be comma-separated string or array)
            $relations = is_array($include) ? $include : explode(',', $include);
            $query->with($relations);
        }

        return $query->get();
    }

    public function findOne(array $condition): ?SeasonSport
    {
        $query = $this->seasonSportRepository->query();

        // Handle where conditions
        if (isset($condition['where'])) {
            $whereConditions = $condition['where'];
            if (is_array($whereConditions)) {
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
        } else {
            // Backward compatibility
            foreach ($condition as $key => $value) {
                if ($key !== 'include') {
                    $query->where($key, $value);
                }
            }
        }

        // Handle include relations
        if (isset($condition['include'])) {
            $query->with($condition['include']);
        }

        return $query->first();
    }

    public function create(array $data): SeasonSport
    {
        return $this->seasonSportRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->seasonSportRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->seasonSportRepository->delete($id);
    }
}

