<?php

namespace App\Services\V5;

use App\Models\V5\TournamentGroup;
use App\Repositories\V5\TournamentGroupRepository;

class TournamentGroupService
{
    public function __construct(protected TournamentGroupRepository $tournamentGroupRepository)
    {
    }

    public function findOne(array $condition): ?TournamentGroup
    {
        // Extract where conditions and include relations
        $whereConditions = $condition['where'] ?? [];
        $includeRelations = $condition['include'] ?? [];

        // If whereConditions is empty but condition has direct keys (for backward compatibility)
        if (empty($whereConditions) && !isset($condition['where']) && !isset($condition['include'])) {
            $whereConditions = $condition;
        }

        return $this->tournamentGroupRepository->findOneBy($whereConditions, ['*'], $includeRelations);
    }

    public function findAll(array $conditions = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->tournamentGroupRepository->query();

        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }

        if (isset($conditions['include'])) {
            $query->with($conditions['include']);
        }

        return $query->get();
    }

    public function create(array $data): TournamentGroup
    {
        return $this->tournamentGroupRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->tournamentGroupRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->tournamentGroupRepository->delete($id);
    }

    public function findAndCountAll(array $conditions = []): array
    {
        $query = $this->tournamentGroupRepository->query();

        // Handle leagueId filter
        if (isset($conditions['leagueId']) && $conditions['leagueId'] !== null && $conditions['leagueId'] !== '') {
            $query->where('league_id', (int)$conditions['leagueId']);
        }

        // Handle searchTerm filter
        if (isset($conditions['searchTerm']) && !empty(trim($conditions['searchTerm']))) {
            $searchTerm = trim($conditions['searchTerm']);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('short_name', 'ILIKE', "%{$searchTerm}%");
            });
        }

        // Handle other filters
        if (isset($conditions['where'])) {
            foreach ($conditions['where'] as $key => $value) {
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

        // Handle includes
        if (isset($conditions['include'])) {
            $query->with($conditions['include']);
        }

        $count = $query->count();

        // Apply ordering
        $orderBy = $conditions['orderBy'] ?? 'id';
        // Map camelCase orderBy to snake_case column names
        $orderByMap = [
            'shortName' => 'short_name',
            'startDate' => 'start_date',
            'endDate' => 'end_date',
        ];
        $orderByColumn = $orderByMap[$orderBy] ?? $orderBy;
        $orderDirection = strtoupper($conditions['orderDirection'] ?? 'ASC');
        $query->orderBy($orderByColumn, $orderDirection);

        // Apply pagination
        $limit = (int)($conditions['limit'] ?? 20);
        $page = (int)($conditions['page'] ?? 1);
        $offset = ($page - 1) * $limit;
        $query->limit($limit);
        $query->offset($offset);

        $rows = $query->get();

        return ['rows' => $rows, 'count' => $count];
    }

    public function getPossibleTeamsForGroup(int $id): \Illuminate\Database\Eloquent\Collection
    {
        $tournamentGroup = $this->findOne([
            'where' => ['id' => $id],
            'include' => ['teams'],
        ]);

        if (!$tournamentGroup) {
            return collect([]);
        }

        $existingTeamIds = $tournamentGroup->teams->pluck('id')->toArray();

        // Get all teams from the league's clubs or all teams if no league
        $query = \App\Models\V5\Team::query();

        if ($tournamentGroup->league_id) {
            $league = \App\Models\V5\League::find($tournamentGroup->league_id);
            if ($league && $league->club_id) {
                $query->where('club_id', $league->club_id);
            }
        }

        if (!empty($existingTeamIds)) {
            $query->whereNotIn('id', $existingTeamIds);
        }

        return $query->get();
    }
}

