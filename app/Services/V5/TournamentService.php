<?php

namespace App\Services\V5;

use App\Models\V5\Tournament;
use App\Repositories\V5\TournamentRepository;
use App\Models\V5\TeamTournament;

class TournamentService
{
    protected ?TeamService $teamService = null;

    public function __construct(
        protected TournamentRepository $tournamentRepository,
    ) {
    }

    /**
     * Lazy load TeamService to avoid deep dependency resolution
     */
    protected function getTeamService(): TeamService
    {
        return $this->teamService ??= app(TeamService::class);
    }

    public function findOne(array $condition): ?Tournament
    {
        // Extract where conditions and include relations
        $whereConditions = $condition['where'] ?? [];
        $includeRelations = $condition['include'] ?? [];

        // If whereConditions is empty but condition has direct keys (for backward compatibility)
        if (empty($whereConditions) && !isset($condition['where']) && !isset($condition['include'])) {
            $whereConditions = $condition;
        }

        $tournament = $this->tournamentRepository->findOneBy($whereConditions, ['*'], $includeRelations);

        // Load relations if tournament found and relations were requested
        if ($tournament && !empty($includeRelations)) {
            $tournament->load($includeRelations);
        }

        return $tournament;
    }

    public function findAll(array $conditions = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->tournamentRepository->query();

        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }

        if (isset($conditions['include'])) {
            $query->with($conditions['include']);
        }

        return $query->get();
    }

    public function create(array $data): Tournament
    {
        return $this->tournamentRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->tournamentRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        try {
            // Delete team tournaments
            TeamTournament::where('tournament_id', $id)->delete();

            // Delete tournament
            return $this->tournamentRepository->delete($id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getPossibleTeamsForTournament(int $id): \Illuminate\Database\Eloquent\Collection
    {
        $tournament = $this->tournamentRepository->query()->with(['teams'])
            ->where('id', $id)
            ->first();

        if (!$tournament) {
            return collect([]);
        }

        $existingTeams = $tournament->teams->pluck('id')->toArray();

        // Build where conditions
        $whereConditions = [];

        // Only exclude existing teams if there are any
        if (!empty($existingTeams)) {
            $whereConditions[] = ['id', 'not in', $existingTeams];
        }

        // If tournament has league_id, filter by league's club teams
        if ($tournament->league_id) {
            $league = \App\Models\V5\League::find($tournament->league_id);
            if ($league && $league->club_id) {
                $whereConditions[] = ['club_id', '=', $league->club_id];
            }
        }

        return $this->getTeamService()->findAll([
            'where' => $whereConditions,
        ]);
    }

    public function findAndCountAll(array $conditions = []): array
    {
        $query = $this->tournamentRepository->query();

        if (isset($conditions['where']) && is_array($conditions['where'])) {
            foreach ($conditions['where'] as $whereCondition) {
                if (is_array($whereCondition)) {
                    if (count($whereCondition) === 3) {
                        $query->where($whereCondition[0], $whereCondition[1], $whereCondition[2]);
                    } elseif (count($whereCondition) === 2) {
                        $query->where($whereCondition[0], $whereCondition[1]);
                    }
                }
            }
        }

        if (isset($conditions['searchTerm'])) {
            $searchTerm = $conditions['searchTerm'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('short_name', 'ILIKE', "%{$searchTerm}%");
            });
        }

        // Handle leagueId filter
        if (isset($conditions['leagueId']) && $conditions['leagueId'] !== null && $conditions['leagueId'] !== '') {
            $query->where('league_id', (int)$conditions['leagueId']);
        }

        // Get count before adding relationships and pagination
        $count = (clone $query)->count();

        // Add relationships
        if (isset($conditions['include']) && is_array($conditions['include'])) {
            $query->with($conditions['include']);
        }

        // Apply ordering
        $query->orderBy($conditions['orderBy'] ?? 'id', $conditions['orderDirection'] ?? 'ASC');

        // Apply pagination
        $limit = (int)($conditions['limit'] ?? 20);
        $page = (int)($conditions['page'] ?? 1);
        $offset = ($page - 1) * $limit;
        $query->limit($limit);
        $query->offset($offset);

        // Get results
        $rows = $query->get();

        return ['rows' => $rows, 'count' => $count];
    }
}

