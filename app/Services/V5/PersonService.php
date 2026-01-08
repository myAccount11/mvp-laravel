<?php

namespace App\Services\V5;

use App\Models\V5\Person;
use App\Repositories\V5\PersonRepository;
use App\Services\V5\UserService;
use App\Services\V5\CoachService;
use App\Services\V5\PlayerService;
use App\Services\V5\SeasonService;
use App\Services\V5\SystemService;
use App\Services\V5\PlayerLicenseService;
use App\Models\V5\UserRole;
use Carbon\Carbon;

class PersonService
{
    protected PersonRepository $personRepository;
    protected UserService $userService;
    protected ?CoachService $coachService = null;
    protected PlayerService $playerService;
    protected SeasonService $seasonService;
    protected SystemService $systemService;
    protected PlayerLicenseService $playerLicenseService;

    public function __construct(
        PersonRepository $personRepository,
        UserService $userService,
        PlayerService $playerService,
        SeasonService $seasonService,
        SystemService $systemService,
        PlayerLicenseService $playerLicenseService
    ) {
        $this->personRepository = $personRepository;
        $this->userService = $userService;
        $this->playerService = $playerService;
        $this->seasonService = $seasonService;
        $this->systemService = $systemService;
        $this->playerLicenseService = $playerLicenseService;
    }

    protected function getCoachService(): CoachService
    {
        return $this->coachService ??= app(CoachService::class);
    }

    public function findAll(string $orderBy = 'id', string $orderDirection = 'asc'): \Illuminate\Database\Eloquent\Collection
    {
        return $this->personRepository->query()->orderBy($orderBy, $orderDirection)->get();
    }

    public function findOne(array $condition): ?Person
    {
        return $this->personRepository->findOneBy($condition);
    }

    public function create(array $data): Person
    {
        return $this->personRepository->create($data);
    }

    public function syncUserWithPerson(int $userId, int $seasonSportId, bool $isCoach = false): int
    {
        $person = $this->personRepository->query()->where('user_id', $userId)
            ->where('season_sport_id', $seasonSportId)
            ->first();

        $user = $this->userService->findOne(['id' => $userId]);
        if (!$user) {
            throw new \Exception('User not found');
        }

        if (!$person) {
            $person = $this->personRepository->query()->where('email', $user->email)->first();

            if (!$person) {
                $person = $this->personRepository->create([
                    'user_id' => $userId,
                    'email' => $user->email,
                    'name' => $user->name,
                    'season_sport_id' => $seasonSportId,
                ]);
            } else {
                if (!$person->user_id) {
                    $this->personRepository->update($person->id, ['user_id' => $userId]);
                }
            }
        }

        $personId = $person->id;

        $seasonSport = $this->seasonService->findOne(['id' => $seasonSportId]);
        if (!$seasonSport) {
            throw new \Exception('Season sport not found');
        }

        $seasonNameParts = explode('/', $seasonSport->name);
        $seasonEnd = $seasonNameParts[1] . '-05-31';
        $formattedDate = Carbon::now()->format('Y-m-d');

        $userRoles = UserRole::where('season_sport_id', $seasonSportId)
            ->where('user_id', $userId)
            ->whereIn('role_id', [5, 6, 7, 8, 11])
            ->where('team_id', '>', 0)
            ->where('user_role_approved_by_user_id', '>', 0)
            ->get();

        $coachAssistant = null;
        $coachTeamManager = null;
        $coachHead = null;
        $player = null;

        foreach ($userRoles as $userRole) {
            switch ($userRole->role_id) {
                case 5:
                    $coachAssistant = $userRole;
                    break;
                case 6:
                    $coachTeamManager = $userRole;
                    break;
                case 7:
                    $coachHead = $userRole;
                    break;
                case 8:
                    $player = $userRole;
                    break;
            }
        }

        // Coach part
        $existingCoach = $this->getCoachService()->findOne(['person_id' => $personId]);

        if (!$existingCoach && ($coachAssistant || $coachTeamManager || $coachHead || $isCoach)) {
            $lastLicense = $this->systemService->findOne(['season_sport_id' => $seasonSportId]);
            if ($lastLicense) {
                $nextLicense = $lastLicense->next_coach_license + 1;

                $this->systemService->update($lastLicense->id, [
                    'next_coach_license' => $nextLicense,
                ]);

                $this->getCoachService()->create([
                    'person_id' => $personId,
                    'license' => $nextLicense,
                    'start' => $formattedDate,
                    'end' => $seasonEnd,
                ]);
            }
        }

        // Player part
        $existingPlayer = $this->playerService->findOne(['person_id' => $personId]);

        $playerId = null;
        if (!$existingPlayer && $player) {
            $newPlayer = $this->playerService->create(['person_id' => $personId]);
            $playerId = $newPlayer->id;
        } elseif ($existingPlayer) {
            $playerId = $existingPlayer->id;
        }

        $playerClubs = UserRole::where('season_sport_id', $seasonSportId)
            ->where('user_id', $userId)
            ->where('role_id', 8)
            ->where('team_id', '>', 0)
            ->where('user_role_approved_by_user_id', '>', 0)
            ->get();

        if ($playerClubs->isNotEmpty() && $playerId) {
            foreach ($playerClubs as $club) {
                $existingPlayerLicense = $this->playerLicenseService->findOne([
                    'player_id' => $playerId,
                    'season_sport_id' => $seasonSportId,
                ]);

                if (!$existingPlayerLicense) {
                    $this->playerLicenseService->create([
                        'start' => $formattedDate,
                        'end' => $seasonEnd,
                        'status' => 'afventer',
                        'club_id' => $club->club_id,
                        'season_sport_id' => $seasonSportId,
                        'player_id' => $playerId,
                    ]);
                }
            }
        }

        return $personId;
    }

    public function updatePersonAndUserName(int $personId, array $body): Person
    {
        $person = $this->personRepository->query()->find($personId);

        if (!$person) {
            throw new \Exception('Person not found');
        }

        $this->personRepository->update($personId, $body);

        if (isset($body['name']) && $person->user_id) {
            $this->userService->updateUser(['id' => $person->user_id], ['name' => $body['name']]);
        }

        return $person->fresh();
    }

    public function savePlayerLicense(array $data): bool
    {
        if (isset($data['user_id'])) {
            $this->syncUserWithPerson($data['user_id'], $data['season_sport_id']);
            return $this->playerLicenseService->updateByCondition(
                ['id' => $data['license_id']],
                [
                    'start' => $data['start'],
                    'end' => $data['end'],
                    'on_contract' => (int)($data['on_contract'] ?? 0),
                ]
            );
        }
        return false;
    }
}
