<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        if ($record) {
            $record->update($data);
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $record = $this->find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }

    public function findBy(array $conditions = [], array $columns = ['*'], array $relations = [], array $order = [])
    {
        $query = $this->model->newQuery();

        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                if (is_array($value)) {
                    if (count($value) === 3) {
                        // Handle ['column', 'operator', 'value'] format
                        $query->where($value[0], $value[1], $value[2]);
                    } elseif (count($value) === 2) {
                        // Handle ['column', 'value'] format
                        $query->where($value[0], $value[1]);
                    }
                } else {
                    $query->where($key, $value);
                }
            }
        }

        if (!empty($relations)) {
            $query->with($relations);
        }

        if (!empty($order)) {
            foreach ($order as $orderItem) {
                if (is_array($orderItem) && count($orderItem) >= 2) {
                    $query->orderBy($orderItem[0], $orderItem[1] ?? 'ASC');
                } elseif (is_string($orderItem)) {
                    $query->orderBy($orderItem, 'ASC');
                }
            }
        }

        return $query->get($columns);
    }

    public function findOneBy(array $conditions, array $columns = ['*'], array $relations = [])
    {
        $query = $this->model->newQuery();

        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                if (count($value) === 3) {
                    $query->where($value[0], $value[1], $value[2]);
                } elseif (count($value) === 2) {
                    $query->where($value[0], $value[1]);
                }
            } else {
                $query->where($key, $value);
            }
        }

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->first($columns);
    }

    public function findAndCountAll(array $options = []): array
    {
        $query = $this->model->newQuery();

        if (isset($options['where'])) {
            foreach ($options['where'] as $key => $value) {
                if (is_array($value)) {
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

        if (isset($options['include'])) {
            $query->with($options['include']);
        }

        $count = $query->count();

        if (isset($options['orderBy'])) {
            $query->orderBy($options['orderBy'], $options['orderDirection'] ?? 'ASC');
        }

        if (isset($options['limit'])) {
            $query->limit($options['limit']);
        }

        if (isset($options['offset'])) {
            $query->offset($options['offset']);
        }

        $rows = $query->get();

        return ['rows' => $rows, 'count' => $count];
    }
}

