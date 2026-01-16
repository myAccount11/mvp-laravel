<?php

namespace App\Services\V5;

use App\Models\V5\Registration;
use App\Repositories\V5\RegistrationRepository;

class RegistrationService
{
    public function __construct(protected RegistrationRepository $registrationRepository)
    {
    }

    public function findOne(array $condition): ?Registration
    {
        return $this->registrationRepository->findOneBy($condition);
    }

    public function findAndCountAll(array $conditions): array
    {
        $orderBy = $conditions['orderBy'] ?? 'id';
        $orderDirection = $conditions['orderDirection'] ?? 'ASC';
        $page = $conditions['page'] ?? 1;
        $limit = $conditions['limit'] ?? 20;
        $searchTerm = $conditions['searchTerm'] ?? null;
        $tournamentId = $conditions['tournamentId'] ?? null;

        $searchConditions = [];

        if ($tournamentId) {
            $searchConditions['tournament_id'] = $tournamentId;
        }

        if ($searchTerm) {
            $searchConditions[] = ['name', 'like', "%{$searchTerm}%"];
        }

        $query = $this->registrationRepository->query()->with('club');

        if (!empty($searchConditions)) {
            $query->where($searchConditions);
        }

        $result = $query->orderBy($orderBy, $orderDirection)
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        $count = $query->count();

        return [
            'rows' => $result,
            'count' => $count,
        ];
    }

    public function saveRegistration(array $data): Registration|string
    {
        $clubId = $data['clubId'] ?? null;
        $tournamentId = $data['tournamentId'] ?? null;
        $level = $data['level'] ?? null;
        $count = $data['count'] ?? null;

        $existingRegistration = $this->registrationRepository->findOneBy([
            'where' => [
                'club_id' => $clubId,
                'tournament_id' => $tournamentId,
                'level' => $level,
            ],
        ]);

        if ($existingRegistration) {
            if ($existingRegistration->count !== $count) {
                $existingRegistration->count = $count;
                $existingRegistration->save();
                return $existingRegistration;
            } else {
                return 'Registration already';
            }
        } else {
            return $this->registrationRepository->create([
                'club_id' => $clubId,
                'tournament_id' => $tournamentId,
                'level' => $level,
                'count' => $count,
            ]);
        }
    }

    public function saveBulkRegistration(array $bulkData): array
    {
        $results = [];

        foreach ($bulkData as $data) {
            $clubId = $data['clubId'] ?? null;
            $tournamentId = $data['tournamentId'] ?? null;
            $level = $data['level'] ?? null;
            $count = $data['count'] ?? null;

            $existingRegistration = $this->registrationRepository->findOneBy([
                'where' => [
                    'club_id' => $clubId,
                    'tournament_id' => $tournamentId,
                    'level' => $level,
                ],
            ]);

            if ($existingRegistration) {
                if ($existingRegistration->count === $count) {
                    $results[] = [
                        'success' => false,
                        'message' => 'Registration already exists with the same count.',
                        'level' => $level,
                    ];
                } elseif ($count === 0 || $count === null) {
                    if ($existingRegistration->count === $count) {
                        $results[] = [
                            'success' => true,
                            'message' => 'Registration already exists, and does not change.',
                            'level' => $level,
                        ];
                    } else {
                        $existingRegistration->count = null;
                        $existingRegistration->save();
                        $results[] = [
                            'success' => true,
                            'message' => 'Registration updated (count set to null)',
                            'level' => $level,
                        ];
                    }
                } elseif (($existingRegistration->count === 0 || $existingRegistration->count === null) && $count > 0) {
                    $existingRegistration->count = $count;
                    $existingRegistration->save();
                    $results[] = [
                        'success' => true,
                        'message' => 'Registration created',
                        'level' => $level,
                    ];
                } elseif ($count > 0) {
                    $existingRegistration->count = $count;
                    $existingRegistration->save();
                    $results[] = [
                        'success' => true,
                        'message' => 'Registration updated',
                        'level' => $level,
                    ];
                }
            } else {
                if ($count > 0) {
                    $this->registrationRepository->create([
                        'club_id' => $clubId,
                        'tournament_id' => $tournamentId,
                        'level' => $level,
                        'count' => $count,
                    ]);

                    $results[] = [
                        'success' => true,
                        'message' => 'Registration created',
                        'level' => $level,
                    ];
                } else {
                    $results[] = [
                        'success' => false,
                        'message' => 'No registration created because count is 0 or null.',
                        'level' => $level,
                    ];
                }
            }
        }

        return $results;
    }

    public function create(array $data): Registration
    {
        return $this->registrationRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->registrationRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->registrationRepository->delete($id);
    }
}

