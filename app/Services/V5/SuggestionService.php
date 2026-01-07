<?php

namespace App\Services\V5;

use App\Models\V5\Suggestion;
use App\Repositories\V5\SuggestionRepository;

class SuggestionService
{
    protected SuggestionRepository $suggestionRepository;

    public function __construct(SuggestionRepository $suggestionRepository)
    {
        $this->suggestionRepository = $suggestionRepository;
    }

    public function findAll(array $conditions = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->suggestionRepository->query();

        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }

        return $query->get();
    }

    public function findOne(array $condition): ?Suggestion
    {
        return $this->suggestionRepository->findOneBy($condition);
    }

    public function create(array $data): Suggestion
    {
        return $this->suggestionRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->suggestionRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->suggestionRepository->delete($id);
    }

    public function updateByCondition(array $conditions, array $data): bool
    {
        try {
            $query = $this->suggestionRepository->query();
            if (isset($conditions['where'])) {
                $query->where($conditions['where']);
            }
            $affected = $query->update($data);
            return $affected > 0;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}

