<?php

namespace App\Services\V5;

use App\Models\V5\TournamentConfig;
use App\Repositories\V5\TournamentConfigRepository;

class TournamentConfigsService
{
    public function __construct(protected TournamentConfigRepository $tournamentConfigRepository)
    {
    }

    public function findOne(array $condition): ?TournamentConfig
    {
        return $this->tournamentConfigRepository->findOneBy($condition);
    }

    public function findAll(array $condition = []): \Illuminate\Database\Eloquent\Collection
    {
        // Normalize camelCase query parameters to snake_case for database columns
        $normalized = $this->normalizeQueryParams($condition);
        // findBy expects a flat array, not nested 'where'
        return $this->tournamentConfigRepository->findBy($normalized);
    }

    /**
     * Normalize camelCase query parameters to snake_case
     * This is only for query parameters, not request body
     */
    protected function normalizeQueryParams(array $params): array
    {
        $normalized = [];
        $fieldMap = [
            'seasonSportId' => 'season_sport_id',
        ];

        foreach ($params as $key => $value) {
            $snakeKey = $fieldMap[$key] ?? $key;
            $normalized[$snakeKey] = $value;
        }

        return $normalized;
    }

    public function findAndCountAll(array $condition): array
    {
        return $this->tournamentConfigRepository->findAndCountAll($condition);
    }

    public function create(array $data): TournamentConfig
    {
        return $this->tournamentConfigRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->tournamentConfigRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->tournamentConfigRepository->delete($id);
    }
}

