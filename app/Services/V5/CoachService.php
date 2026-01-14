<?php

namespace App\Services\V5;

use App\Models\V5\Coach;
use App\Repositories\V5\CoachRepository;
use App\Services\V5\UserService;
use App\Services\V5\PersonService;
use App\Services\V5\TournamentGroupService;
use App\Services\V5\TeamService;
use App\Services\V5\CoachLicenseService;
use App\Services\MailService;
use App\Models\V5\Person;
use App\Models\V5\User;
use App\Models\V5\UserRole;
use App\Models\V5\Team;
use App\Models\V5\TournamentGroup;
use App\Models\V5\Club;
use App\Models\V5\CoachEducation;
use App\Models\V5\CoachHistory;

class CoachService
{
    public function __construct(
        protected CoachRepository        $coachRepository,
        protected UserService            $userService,
        protected PersonService          $personService,
        protected TournamentGroupService $tournamentGroupService,
        protected TeamService            $teamService,
        protected CoachLicenseService    $coachLicenseService,
        protected MailService            $mailService
    )
    {
    }

    public function findAllCoachesByFiltersOrWithout(array $queryParams): array
    {
        $orderBy = $queryParams['orderBy'] ?? 'id';
        $orderDirection = $queryParams['orderDirection'] ?? 'ASC';
        $page = $queryParams['page'] ?? 1;
        $limit = $queryParams['limit'] ?? 20;
        $searchTerm = $queryParams['searchTerm'] ?? null;
        $tournamentGroupId = $queryParams['tournamentGroupId'] ?? null;
        $ageGroup = $queryParams['ageGroup'] ?? null;

        $personConditions = [];
        $coachConditions = [];
        $coachIds = [];

        if ($tournamentGroupId || $ageGroup) {
            $teamIds = [];

            if ($tournamentGroupId) {
                $tournamentGroup = $this->tournamentGroupService->findOne(['id' => $tournamentGroupId]);
                if ($tournamentGroup) {
                    $teamIds = $tournamentGroup->teams->pluck('id')->toArray();
                }
            }

            if ($ageGroup) {
                $teamsByAgeGroup = $this->teamService->findAll(['where' => [['age_group', $ageGroup]]]);
                if ($teamsByAgeGroup->isEmpty()) {
                    return ['count' => 0, 'rows' => []];
                }

                $teamsByAgeGroupIds = $teamsByAgeGroup->pluck('id')->toArray();
                $teamIds = $tournamentGroupId
                    ? array_intersect($teamIds, $teamsByAgeGroupIds)
                    : $teamsByAgeGroupIds;
            }

            if (empty($teamIds)) {
                return ['count' => 0, 'rows' => []];
            }

            $userRoles = UserRole::whereIn('role_id', [5, 6])
                ->whereIn('team_id', $teamIds)
                ->get();

            $coachIds = $userRoles->pluck('user_id')->unique()->toArray();

            if (empty($coachIds)) {
                return ['count' => 0, 'rows' => []];
            }
        }

        if ($searchTerm) {
            if (is_numeric($searchTerm)) {
                $coachConditions[] = ['license', '=', $searchTerm];
            } else {
                $personConditions[] = function ($query) use ($searchTerm) {
                    $query->where('name', 'ILIKE', "%{$searchTerm}%")
                        ->orWhere('email', 'ILIKE', "%{$searchTerm}%");
                };
            }
        }

        $query = $this->coachRepository->query();

        if (!empty($coachConditions)) {
            foreach ($coachConditions as $condition) {
                if (is_array($condition)) {
                    $query->where($condition[0], $condition[1], $condition[2]);
                }
            }
        }

        $query->with([
            'person' => function ($q) use ($personConditions, $coachIds) {
                if (!empty($coachIds)) {
                    $q->whereIn('user_id', $coachIds);
                }
                if (!empty($personConditions)) {
                    foreach ($personConditions as $condition) {
                        if (is_callable($condition)) {
                            $condition($q);
                        }
                    }
                }
                $q->with(['user.userRoles.team.tournamentGroup', 'user.userRoles.team.club', 'user.userRoles.club', 'user.userRoles.role']);
            },
            'coachEducation',
            'coachHistories'
        ]);

        if ($orderBy === 'email' || $orderBy === 'name') {
            $query->join('person', 'coach.person_id', '=', 'person.id')
                ->orderBy("person.{$orderBy}", $orderDirection);
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }

        $count = $query->count();
        $query->limit($limit);
        $query->offset(($page - 1) * $limit);

        $rows = $query->get();

        return ['rows' => $rows, 'count' => $count];
    }

    public function createCoach(array $data): int
    {
        $name = $data['name'];
        $email = $data['email'];
        $seasonSportId = $data['season_sport_id'];

        $user = $this->userService->findOne(['email' => $email]);

        if (!$user) {
            $user = $this->userService->createUserByAdmin(['name' => $name, 'email' => $email]);
        }

        return $this->personService->syncUserWithPerson($user->id, $seasonSportId, true);
    }

    public function findOneCoach(array $condition): ?Coach
    {
        return $this->coachRepository->query()->with('person')->where($condition)->first();
    }

    public function coachInfo(array $coachInfo): array
    {
        $coachId = $coachInfo['coach_id'] ?? null;
        if (!$coachId) {
            throw new \Exception('coachId is required but was not provided.');
        }

        $coach = $this->coachRepository->query()->with('person')->find($coachId);
        if (!$coach) {
            throw new \Exception('Coach not found');
        }

        $formatDate = function ($date) {
            if (!$date || !str_contains($date, '-')) {
                return null;
            }
            $parts = explode('-', $date);
            if (count($parts) === 3) {
                return "{$parts[0]}-{$parts[1]}-{$parts[2]}";
            }
            return $date;
        };

        $licenseStart = $formatDate($coachInfo['start'] ?? null);
        $licenseEnd = $formatDate($coachInfo['end'] ?? null);

        $updateData = [];
        if (!empty($coachInfo['level'])) {
            $updateData['level'] = $coachInfo['level'];
        }
        if ($licenseStart) {
            $updateData['start'] = $licenseStart;
        }
        if ($licenseEnd) {
            $updateData['end'] = $licenseEnd;
        }
        $updateData['master_license'] = null;

        $this->coachRepository->update($coachId, $updateData);

        $updateOrCreateLicense = function ($typeId, $start, $end) use ($coachId, $coach, $formatDate) {
            $formattedStart = $formatDate($start);
            $formattedEnd = $formatDate($end);

            $coachLicense = $this->coachLicenseService->findOne([
                'coach_id'              => $coachId,
                'coach_license_type_id' => $typeId,
            ]);

            if ($formattedStart && $formattedEnd) {
                if ($coachLicense) {
                    $this->coachLicenseService->update($coachLicense->id, [
                        'deleted' => false,
                        'start'   => $formattedStart,
                        'end'     => $formattedEnd,
                    ]);
                } else {
                    $coachLicense = $this->coachLicenseService->create([
                        'coach_license_type_id' => $typeId,
                        'coach_id'              => $coachId,
                        'start'                 => $formattedStart,
                        'end'                   => $formattedEnd,
                    ]);
                }

                $this->mailService->sendMailCongretsCoach(
                    $coach->person->email,
                    $coach->person->name,
                    $coach->level,
                    $coachLicense->start,
                    $coachLicense->end
                );
            } elseif ($coachLicense) {
                $this->coachLicenseService->update($coachLicense->id, ['deleted' => true]);
            }
        };

        $updateOrCreateLicense(1, $coachInfo['startm'] ?? null, $coachInfo['endm'] ?? null);
        $updateOrCreateLicense(2, $coachInfo['startb'] ?? null, $coachInfo['endb'] ?? null);
        $updateOrCreateLicense(3, $coachInfo['startt'] ?? null, $coachInfo['endt'] ?? null);

        $failedChildren = [];

        if (!empty($coachInfo['children'])) {
            $childrenArray = explode(',', $coachInfo['children']);
            foreach ($childrenArray as $child) {
                $child = trim($child);
                if (is_numeric($child)) {
                    $existingChild = $this->coachRepository->findOneBy(['license' => $child]);
                    if ($existingChild) {
                        $this->coachRepository->update($existingChild->id, [
                            'master_license' => $coach->license,
                        ]);
                    } else {
                        $failedChildren[] = $child;
                    }
                } else {
                    $failedChildren[] = $child;
                }
            }
        }

        $this->mailService->sendMailCongretsCoach(
            $coach->person->email,
            $coach->person->name,
            $coach->level,
            $coach->start,
            $coach->end
        );

        return !empty($failedChildren)
            ? ['message' => 'success', 'failed_children' => $failedChildren]
            : ['message' => 'success'];
    }

    public function findOne(array $condition): ?Coach
    {
        return $this->coachRepository->findOneBy($condition);
    }

    public function create(array $data): Coach
    {
        return $this->coachRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->coachRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->coachRepository->delete($id);
    }
}

