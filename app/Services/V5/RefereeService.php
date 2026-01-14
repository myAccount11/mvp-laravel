<?php

namespace App\Services\V5;

use App\Models\V5\Referee;
use App\Repositories\V5\RefereeRepository;
use App\Services\V5\UserService;
use App\Services\V5\PersonService;
use App\Models\V5\User;
use App\Models\V5\UserRole;

class RefereeService
{
    public function __construct(
        protected RefereeRepository $refereeRepository,
        protected UserService $userService,
        protected PersonService $personService
    ) {
    }

    public function findAllReferees(array $queryParams): array
    {
        $orderBy = $queryParams['orderBy'] ?? 'id';
        $orderDirection = $queryParams['orderDirection'] ?? 'ASC';
        $page = $queryParams['page'] ?? 1;
        $limit = $queryParams['limit'] ?? 20;
        $searchTerm = $queryParams['searchTerm'] ?? null;

        $personConditions = [];
        $refereeConditions = [];

        if ($searchTerm) {
            if (is_numeric($searchTerm)) {
                $refereeConditions[] = ['license', '=', $searchTerm];
            } else {
                $personConditions[] = function ($query) use ($searchTerm) {
                    $query->where('name', 'ILIKE', "%{$searchTerm}%")
                          ->orWhere('email', 'ILIKE', "%{$searchTerm}%");
                };
            }
        }

        $query = $this->refereeRepository->query();

        if (!empty($refereeConditions)) {
            foreach ($refereeConditions as $condition) {
                if (is_array($condition)) {
                    $query->where($condition[0], $condition[1], $condition[2]);
                }
            }
        }

        $query->with(['user' => function ($q) use ($personConditions) {
            if (!empty($personConditions)) {
                foreach ($personConditions as $condition) {
                    if (is_callable($condition)) {
                        $condition($q);
                    }
                }
            }
        }]);

        if ($orderBy === 'email' || $orderBy === 'name') {
            $query->join('users', 'referee.user_id', '=', 'users.id')
                  ->orderBy("users.{$orderBy}", $orderDirection);
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }

        $count = $query->count();
        $query->limit($limit);
        $query->offset(($page - 1) * $limit);

        $rows = $query->get();

        return ['rows' => $rows, 'count' => $count];
    }

    public function createRef(array $createRefereeDto): string
    {
        $name = $createRefereeDto['name'];
        $email = $createRefereeDto['email'];
        $seasonSportId = $createRefereeDto['season_sport_id'];
        $refId = $createRefereeDto['ref_id'] ?? null;

        if ($refId === null) {
            $user = $this->userService->findOne(['email' => $email]);

            if (!$user) {
                $user = $this->userService->createUserByAdmin([
                    'name' => $name,
                    'email' => $email,
                    'country' => $createRefereeDto['country'] ?? null,
                    'city' => $createRefereeDto['city'] ?? null,
                    'address_line1' => $createRefereeDto['address_line1'] ?? null,
                    'address_line2' => $createRefereeDto['address_line2'] ?? null,
                    'postal_code' => $createRefereeDto['postal_code'] ?? null,
                    'debtor_number' => $createRefereeDto['debtor_number'] ?? null,
                    'phone_numbers' => $createRefereeDto['phone_numbers'] ?? null,
                ]);

                UserRole::create([
                    'role_id' => 11,
                    'user_id' => $user->id,
                    'user_role_approved_by_user_id' => 1,
                    'season_sport_id' => $seasonSportId,
                ]);

                $userLicense = 50000000 + 2 * $user->id;
                $this->refereeRepository->create([
                    'user_id' => $user->id,
                    'license' => $userLicense,
                    'is_active' => true,
                    'prio' => $createRefereeDto['prio'] ?? null,
                    'prio_max' => $createRefereeDto['prio_max'] ?? null,
                    'max_star_rating' => $createRefereeDto['max_star_rating'] ?? null,
                    'mentor' => $createRefereeDto['mentor'] ?? false,
                    'prospect' => $createRefereeDto['prospect'] ?? false,
                    'only_with_better' => $createRefereeDto['only_with_better'] ?? false,
                    'reserve' => $createRefereeDto['reserve'] ?? false,
                    'commisioner_level' => $createRefereeDto['commisioner_level'] ?? null,
                    'evaluator_level' => $createRefereeDto['evaluator_level'] ?? null,
                    'can_three' => $createRefereeDto['can_three'] ?? false,
                ]);
            } else {
                $this->userService->updateUser(
                    ['id' => $user->id],
                    [
                        'name' => $name,
                        'email' => $email,
                        'country' => $createRefereeDto['country'] ?? null,
                        'city' => $createRefereeDto['city'] ?? null,
                        'address_line1' => $createRefereeDto['address_line1'] ?? null,
                        'address_line2' => $createRefereeDto['address_line2'] ?? null,
                        'postal_code' => $createRefereeDto['postal_code'] ?? null,
                        'debtor_number' => $createRefereeDto['debtor_number'] ?? null,
                        'phone_numbers' => $createRefereeDto['phone_numbers'] ?? null,
                    ]
                );

                UserRole::firstOrCreate(
                    [
                        'season_sport_id' => $seasonSportId,
                        'user_id' => $user->id,
                        'role_id' => 11,
                        'user_role_approved_by_user_id' => ['>', 0],
                    ],
                    [
                        'role_id' => 11,
                        'user_id' => $user->id,
                        'user_role_approved_by_user_id' => 1,
                        'season_sport_id' => $seasonSportId,
                    ]
                );

                $existingReferee = $this->refereeRepository->findOneBy(['user_id' => $user->id]);
                $userLicense = $user->license ?? (50000000 + 2 * $user->id);

                if (!$existingReferee) {
                    $this->refereeRepository->create([
                        'user_id' => $user->id,
                        'license' => $userLicense,
                        'is_active' => true,
                        'prio' => $createRefereeDto['prio'] ?? null,
                        'prio_max' => $createRefereeDto['prio_max'] ?? null,
                        'max_star_rating' => $createRefereeDto['max_star_rating'] ?? null,
                        'mentor' => $createRefereeDto['mentor'] ?? false,
                        'prospect' => $createRefereeDto['prospect'] ?? false,
                        'only_with_better' => $createRefereeDto['only_with_better'] ?? false,
                        'reserve' => $createRefereeDto['reserve'] ?? false,
                        'commisioner_level' => $createRefereeDto['commisioner_level'] ?? null,
                        'evaluator_level' => $createRefereeDto['evaluator_level'] ?? null,
                        'can_three' => $createRefereeDto['can_three'] ?? false,
                    ]);
                } else {
                    if (!$existingReferee->license || trim($existingReferee->license) === '0') {
                        $userLicense = 50000000 + 2 * $existingReferee->user_id;
                    }

                    $updateData = $this->mapRefereeDto($createRefereeDto);
                    if ($userLicense) {
                        $updateData['license'] = $userLicense;
                    }

                    $this->refereeRepository->update($existingReferee->id, $updateData);
                }
            }
            return 'success';
        }

        $referee = $this->refereeRepository->query()->with('user')->find($refId);
        if (!$referee) {
            return "Didn't exist with this id referee.";
        }

        $currentLicense = $referee->license ? trim($referee->license) : null;
        $newLicense = (!$currentLicense || $currentLicense === '0')
            ? 50000000 + 2 * $referee->user_id
            : null;

        if ($referee && $referee->user) {
            $updateData = $this->mapRefereeDto($createRefereeDto);
            if ($newLicense) {
                $updateData['license'] = $newLicense;
            }

            $this->refereeRepository->update($referee->id, $updateData);

            $this->userService->updateUser(
                ['id' => $referee->user->id],
                [
                    'name' => $name,
                    'email' => $email,
                    'country' => $createRefereeDto['country'] ?? null,
                    'city' => $createRefereeDto['city'] ?? null,
                    'address_line1' => $createRefereeDto['address_line1'] ?? null,
                    'address_line2' => $createRefereeDto['address_line2'] ?? null,
                    'postal_code' => $createRefereeDto['postal_code'] ?? null,
                    'debtor_number' => $createRefereeDto['debtor_number'] ?? null,
                    'phone_numbers' => $createRefereeDto['phone_numbers'] ?? null,
                ]
            );

            return 'success';
        }

        return 'error';
    }

    private function mapRefereeDto(array $createRefereeDto): array
    {
        return [
            'prio' => $createRefereeDto['prio'] ?? null,
            'prio_max' => $createRefereeDto['prio_max'] ?? null,
            'max_star_rating' => $createRefereeDto['max_star_rating'] ?? null,
            'mentor' => $createRefereeDto['mentor'] ?? false,
            'prospect' => $createRefereeDto['prospect'] ?? false,
            'only_with_better' => $createRefereeDto['only_with_better'] ?? false,
            'is_active' => $createRefereeDto['is_active'] ?? true,
            'reserve' => $createRefereeDto['reserve'] ?? false,
            'commisioner_level' => $createRefereeDto['commisioner_level'] ?? null,
            'evaluator_level' => $createRefereeDto['evaluator_level'] ?? null,
            'can_three' => $createRefereeDto['can_three'] ?? false,
        ];
    }

    public function findOne(array $condition): ?Referee
    {
        return $this->refereeRepository->findOneBy($condition);
    }

    public function delete(int $id): bool
    {
        return $this->refereeRepository->delete($id);
    }
}

