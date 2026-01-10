<?php

namespace App\Services\V5;

use App\Models\V5\Court;
use App\Repositories\V5\CourtRepository;
use App\Repositories\V5\CourtUsageRepository;
use App\Models\V5\CourtUsage;

class CourtService
{
    protected CourtRepository $courtRepository;
    protected CourtUsageRepository $courtUsageRepository;

    public function __construct(
        CourtRepository $courtRepository,
        CourtUsageRepository $courtUsageRepository
    ) {
        $this->courtRepository = $courtRepository;
        $this->courtUsageRepository = $courtUsageRepository;
    }

    public function getCourts(int $limit, int $offset): array
    {
        $query = $this->courtRepository->query()->with('courtUsages')
            ->orderBy('id');

        $count = $query->count();
        $rows = $query->limit($limit)->offset($offset)->get();

        return ['rows' => $rows, 'count' => $count];
    }

    public function findAll(array $conditions = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->courtRepository->query();

        if (isset($conditions['where'])) {
            if (is_callable($conditions['where'])) {
                // Handle closure functions
                $query->where($conditions['where']);
            } elseif (is_array($conditions['where'])) {
                // Handle array of conditions
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
            } else {
                $query->where($conditions['where']);
            }
        }

        if (isset($conditions['include'])) {
            $query->with($conditions['include']);
        }

        if (isset($conditions['order'])) {
            foreach ($conditions['order'] as $order) {
                if (is_array($order) && count($order) >= 2) {
                    $query->orderBy($order[0], $order[1] ?? 'ASC');
                }
            }
        }

        return $query->get();
    }

    public function findOne(array $condition): ?Court
    {
        return $this->courtRepository->findOneBy($condition);
    }

    public function createCourt(array $createCourtDto, array $courtRequirements = []): Court
    {
        $court = $this->courtRepository->create($createCourtDto);

        $courtRequirement1 = $courtRequirements['court_requirement_1'] ?? 0;
        $courtRequirement2 = $courtRequirements['court_requirement_2'] ?? 0;
        $courtRequirement3 = $courtRequirements['court_requirement_3'] ?? 0;
        $courtRequirement4 = $courtRequirements['court_requirement_4'] ?? 0;

        $this->insertOrUpdateCourtUsage($court->id, (int)$courtRequirement1, 1);
        $this->insertOrUpdateCourtUsage($court->id, (int)$courtRequirement2, 2);
        $this->insertOrUpdateCourtUsage($court->id, (int)$courtRequirement3, 3);
        $this->insertOrUpdateCourtUsage($court->id, (int)$courtRequirement4, 4);

        return $court;
    }

    public function updateCourt(int $id, array $updateCourtDto, array $courtRequirements = []): array
    {
        $affected = $this->courtRepository->update($id, $updateCourtDto);

        if ($affected === 0) {
            return ['message' => 'No court found with the specified ID or no changes made'];
        }

        $courtRequirement1 = $courtRequirements['court_requirement_1'] ?? 0;
        $courtRequirement2 = $courtRequirements['court_requirement_2'] ?? 0;
        $courtRequirement3 = $courtRequirements['court_requirement_3'] ?? 0;
        $courtRequirement4 = $courtRequirements['court_requirement_4'] ?? 0;

        $this->insertOrUpdateCourtUsage($id, (int)$courtRequirement1, 1);
        $this->insertOrUpdateCourtUsage($id, (int)$courtRequirement2, 2);
        $this->insertOrUpdateCourtUsage($id, (int)$courtRequirement3, 3);
        $this->insertOrUpdateCourtUsage($id, (int)$courtRequirement4, 4);

        return ['message' => 'Court updated successfully', 'affected_rows' => $affected];
    }

    public function insertOrUpdateCourtUsage(int $courtId, ?int $usageCount, ?int $requirementId): void
    {
        // Skip if requirementId is null or invalid
        if (!$requirementId || $requirementId < 1 || $requirementId > 4) {
            return;
        }

        $existingUsage = $this->courtUsageRepository->findOneBy([
            'court_id' => $courtId,
            'court_requirement_id' => $requirementId,
        ]);

        $count = $usageCount ?? 0;

        if ($existingUsage) {
            // Update existing usage, or delete if count is 0
            if ($count > 0) {
                $this->courtUsageRepository->update($existingUsage->id, [
                    'court_usage_count' => $count,
                ]);
            } else {
                $this->courtUsageRepository->delete($existingUsage->id);
            }
        } else {
            // Only create if count is greater than 0
            if ($count > 0) {
                $this->courtUsageRepository->create([
                    'court_id' => $courtId,
                    'court_requirement_id' => $requirementId,
                    'court_usage_count' => $count,
                ]);
            }
        }
    }

    public function delete(int $id): bool
    {
        return $this->courtRepository->delete($id);
    }
}

