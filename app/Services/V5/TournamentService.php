<?php

namespace App\Services\V5;

use App\Models\V5\Tournament;
use App\Repositories\V5\TournamentRepository;
use App\Repositories\V5\PoolRepository;
use App\Services\V5\TeamService;
use App\Models\V5\TeamTournament;
use App\Models\V5\Team;
use App\Models\V5\TournamentGroup;

class TournamentService
{
    protected TournamentRepository $tournamentRepository;
    protected PoolRepository $poolRepository;
    protected ?TeamService $teamService = null;

    public function __construct(
        TournamentRepository $tournamentRepository,
        PoolRepository $poolRepository
    ) {
        $this->tournamentRepository = $tournamentRepository;
        $this->poolRepository = $poolRepository;
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
            // Delete pools
            $this->poolRepository->query()->where('tournament_id', $id)->delete();

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
        $tournament = $this->tournamentRepository->query()->with(['teams', 'tournamentGroup.teams'])
            ->where('id', $id)
            ->first();

        if (!$tournament || !$tournament->tournamentGroup) {
            return collect([]);
        }

        $allTeams = $tournament->tournamentGroup->teams->pluck('id')->toArray();
        $existingTeams = $tournament->teams->pluck('id')->toArray();

        // If no teams in tournament group, return empty collection
        if (empty($allTeams)) {
            return collect([]);
        }

        // Build where conditions
        $whereConditions = [];
        
        // Always filter by teams in the tournament group
        $whereConditions[] = ['id', 'in', $allTeams];
        
        // Only exclude existing teams if there are any
        if (!empty($existingTeams)) {
            $whereConditions[] = ['id', 'not in', $existingTeams];
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
                $q->where('alias', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('short_name', 'ILIKE', "%{$searchTerm}%");
            });
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

