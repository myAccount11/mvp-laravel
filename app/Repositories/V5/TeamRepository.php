<?php

namespace App\Repositories\V5;

use App\Models\V5\Team;
use App\Repositories\BaseRepository;

class TeamRepository extends BaseRepository
{
    public function __construct(Team $model)
    {
        parent::__construct($model);
    }

    public function findAndCountAll(array $options = []): array
    {
        $query = $this->model->query();

        // Map old $conditions format to $options format for backward compatibility
        $conditions = $options;

        if (isset($conditions['search_term'])) {
            $searchTerm = $conditions['search_term'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('local_name', 'ilike', "%{$searchTerm}%")
                  ->orWhere('tournament_name', 'ilike', "%{$searchTerm}%");
            });
        }

        if (isset($conditions['club_id'])) {
            if (is_array($conditions['club_id'])) {
                $query->whereIn('club_id', $conditions['club_id']);
            } else {
                $query->where('club_id', $conditions['club_id']);
            }
        }

        if (isset($conditions['team_ids'])) {
            $teamIds = is_array($conditions['team_ids'])
                ? $conditions['team_ids']
                : json_decode($conditions['team_ids'], true);
            if (is_array($teamIds) && count($teamIds) > 0) {
                $query->whereIn('id', $teamIds);
            }
        }

        if (isset($conditions['season_sport_id']) && !!$conditions['season_sport_id']) {
            $query->whereHas('teamSeasonSports', function($q) use ($conditions) {
                $q->where('season_sport_id', $conditions['season_sport_id']);
            });
        }

        $orderBy = $conditions['order_by'] ?? 'id';
        // Normalize camelCase to snake_case for order_by
        $orderBy = $this->normalizeOrderBy($orderBy);
        $orderDirection = $conditions['order_direction'] ?? 'ASC';
        $query->orderBy($orderBy, $orderDirection);

        $limit = $conditions['limit'] ?? 20;
        $page = $conditions['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $count = $query->count();
        $rows = $query->with(['club', 'teamSeasonSports'])
            ->offset($offset)
            ->limit($limit)
            ->get();

        return ['rows' => $rows, 'count' => $count];
    }

    public function getLastLicense()
    {
        return $this->model->orderBy('license', 'desc')->value('license') ?? 0;
    }

    /**
     * Normalize camelCase order_by parameter to snake_case
     */
    protected function normalizeOrderBy(string $orderBy): string
    {
        $fieldMap = [
            'tournamentName' => 'tournament_name',
            'localName' => 'local_name',
            'clubId' => 'club_id',
            'ageGroup' => 'age_group',
            'officialTypeId' => 'official_type_id',
            'officialTeamId' => 'official_team_id',
            'clubRank' => 'club_rank',
        ];

        return $fieldMap[$orderBy] ?? $orderBy;
    }
}

