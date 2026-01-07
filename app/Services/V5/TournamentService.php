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
    protected TeamService $teamService;

    public function __construct(
        TournamentRepository $tournamentRepository,
        PoolRepository $poolRepository,
        TeamService $teamService
    ) {
        $this->tournamentRepository = $tournamentRepository;
        $this->poolRepository = $poolRepository;
        $this->teamService = $teamService;
    }

    public function findOne(array $condition): ?Tournament
    {
        return $this->tournamentRepository->findOneBy($condition);
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

        if (!$tournament) {
            return collect([]);
        }

        $allTeams = $tournament->tournamentGroup->teams->pluck('id')->toArray();
        $existingTeams = $tournament->teams->pluck('id')->toArray();

        return $this->teamService->findAll([
            'where' => [
                ['id', 'not in', $existingTeams],
                ['id', 'in', $allTeams],
            ],
        ]);
    }

    public function findAndCountAll(array $conditions = []): array
    {
        $query = $this->tournamentRepository->query();

        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }

        if (isset($conditions['searchTerm'])) {
            $searchTerm = $conditions['searchTerm'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('alias', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('short_name', 'ILIKE', "%{$searchTerm}%");
            });
        }

        $query->with($conditions['include'] ?? []);

        $count = $query->count();

        $query->orderBy($conditions['orderBy'] ?? 'id', $conditions['orderDirection'] ?? 'ASC');
        $query->limit($conditions['limit'] ?? 20);
        $query->offset(((($conditions['page'] ?? 1) - 1) * ($conditions['limit'] ?? 20)));

        $rows = $query->get();

        return ['rows' => $rows, 'count' => $count];
    }
}

