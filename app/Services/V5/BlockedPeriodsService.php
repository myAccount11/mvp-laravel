<?php

namespace App\Services\V5;

use App\Models\V5\BlockedPeriod;
use App\Models\V5\BlockedPeriodTournamentGroup;
use App\Repositories\V5\BlockedPeriodRepository;

class BlockedPeriodsService
{
    protected BlockedPeriodRepository $blockedPeriodRepository;

    public function __construct(BlockedPeriodRepository $blockedPeriodRepository)
    {
        $this->blockedPeriodRepository = $blockedPeriodRepository;
    }

    public function findOne(array $condition): ?BlockedPeriod
    {
        return $this->blockedPeriodRepository->findOneBy($condition);
    }

    public function findAll(array $condition = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->blockedPeriodRepository->findBy($condition);
    }

    public function findAndCountAll(array $condition): array
    {
        return $this->blockedPeriodRepository->findAndCountAll($condition);
    }

    public function create(array $data): BlockedPeriod
    {
        $groups = $data['groups'] ?? [];
        unset($data['groups']);
        
        $period = $this->blockedPeriodRepository->create($data);

        if (!($data['blockAll'] ?? false)) {
            $this->attachToGroups($period->id, $groups);
        }

        return $period;
    }

    public function updatePeriod(int $id, array $data): bool
    {
        $groups = $data['groups'] ?? [];
        unset($data['groups']);

        $period = $this->blockedPeriodRepository->findOneBy([
            'where' => ['id' => $id],
            'include' => ['tournamentGroups'],
        ]);

        if (!$period) {
            return false;
        }

        $period->update($data);

        if (!($data['blockAll'] ?? false)) {
            $existingGroups = $period->tournamentGroups
                ->filter(function ($group) use ($groups) {
                    return in_array($group->id, $groups);
                })
                ->map(function ($group) {
                    return $group->id;
                })
                ->toArray();

            BlockedPeriodTournamentGroup::where('blocked_period_id', $id)
                ->whereNotIn('tournament_group_id', $groups)
                ->delete();

            $newGroups = array_filter($groups, function ($group) use ($existingGroups) {
                return !in_array($group, $existingGroups);
            });

            $this->attachToGroups($id, $newGroups);
        } else {
            BlockedPeriodTournamentGroup::where('blocked_period_id', $id)->delete();
        }

        return true;
    }

    protected function attachToGroups(int $id, array $groups): void
    {
        $data = array_map(function ($group) use ($id) {
            return [
                'blocked_period_id' => $id,
                'tournament_group_id' => $group,
            ];
        }, $groups);

        BlockedPeriodTournamentGroup::insert($data);
    }

    public function delete(int $id): bool
    {
        return $this->blockedPeriodRepository->delete($id);
    }
}

