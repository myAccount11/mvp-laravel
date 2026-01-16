<?php

namespace App\Services\V5;

use App\Models\V5\BlockedPeriod;
use App\Models\V5\BlockedPeriodTournament;
use App\Repositories\V5\BlockedPeriodRepository;
use Illuminate\Database\Eloquent\Collection;

class BlockedPeriodsService
{
    public function __construct(protected BlockedPeriodRepository $blockedPeriodRepository)
    {
    }

    public function findOne(array $condition): ?BlockedPeriod
    {
        $query = $this->blockedPeriodRepository->query();

        // Handle where conditions
        if (isset($condition['where'])) {
            $whereConditions = $condition['where'];
            if (is_array($whereConditions)) {
                foreach ($whereConditions as $key => $value) {
                    if (is_callable($value)) {
                        // Handle closure functions
                        $query->where($value);
                    } elseif (is_array($value)) {
                        // Handle array conditions like ['column', 'operator', 'value']
                        if (count($value) === 3) {
                            $query->where($value[0], $value[1], $value[2]);
                        } elseif (count($value) === 2) {
                            $query->where($value[0], $value[1]);
                        }
                    } else {
                        // Handle simple key-value pairs
                        $query->where($key, $value);
                    }
                }
            } elseif (is_callable($whereConditions)) {
                $query->where($whereConditions);
            }
        }

        // Handle include relations
        if (isset($condition['include'])) {
            $query->with($condition['include']);
        }

        return $query->first();
    }

    public function findAll(array $condition = []): Collection
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
            'include' => ['tournaments'],
        ]);

        if (!$period) {
            return false;
        }

        $period->update($data);

        if (!($data['blockAll'] ?? false)) {
            $existingGroups = $period->tournaments
                ->filter(function ($group) use ($groups) {
                    return in_array($group->id, $groups);
                })
                ->map(function ($group) {
                    return $group->id;
                })
                ->toArray();

            BlockedPeriodTournament::where('blocked_period_id', $id)
                ->whereNotIn('tournament_id', $groups)
                ->delete();

            $newGroups = array_filter($groups, function ($group) use ($existingGroups) {
                return !in_array($group, $existingGroups);
            });

            $this->attachToGroups($id, $newGroups);
        } else {
            BlockedPeriodTournament::where('blocked_period_id', $id)->delete();
        }

        return true;
    }

    protected function attachToGroups(int $id, array $groups): void
    {
        $data = array_map(function ($group) use ($id) {
            return [
                'blocked_period_id' => $id,
                'tournament_id' => $group,
            ];
        }, $groups);

        BlockedPeriodTournament::insert($data);
    }

    public function delete(int $id): bool
    {
        return $this->blockedPeriodRepository->delete($id);
    }
}

