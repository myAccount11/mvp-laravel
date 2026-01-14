<?php

namespace App\Services\V5;

use App\Models\V5\CourtPriority;
use App\Repositories\V5\CourtPriorityRepository;

class CourtPriorityService
{
    public function __construct(protected CourtPriorityRepository $courtPriorityRepository)
    {
    }

    public function findOne(array $condition): ?CourtPriority
    {
        return $this->courtPriorityRepository->findOneBy($condition);
    }

    public function create(array $data): CourtPriority
    {
        return $this->courtPriorityRepository->create($data);
    }

    public function createAndUpdate(array $priorities, int $clubId): array
    {
        $this->courtPriorityRepository->query()->where('club_id', $clubId)->delete();

        $data = array_map(function ($priority, $index) {
            return [
                'club_id' => $priority['clubId'] ?? null,
                'court_id' => $priority['courtId'] ?? null,
                'court_priority_number' => $index + 1,
                'team_id' => $priority['teamId'] ?? null,
            ];
        }, $priorities, array_keys($priorities));

        return $this->courtPriorityRepository->query()->insert($data);
    }

    public function delete(int $id): bool
    {
        return $this->courtPriorityRepository->delete($id);
    }
}

