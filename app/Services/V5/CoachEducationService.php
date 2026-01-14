<?php

namespace App\Services\V5;

use App\Models\V5\CoachEducation;
use App\Models\V5\CoachEducationLicenseType;
use App\Repositories\V5\CoachEducationRepository;

class CoachEducationService
{
    public function __construct(
        protected CoachEducationRepository $coachEducationRepository,
        protected CoachService $coachService,
        protected UserService $userService,
        protected UserSeasonSportsService $userSeasonSportsService,
        protected PersonService $personService,
        protected SeasonService $seasonService,
        protected CoachLicenseService $coachLicenseService
    ) {
    }

    public function findOne(array $condition): ?CoachEducation
    {
        return $this->coachEducationRepository->findOneBy($condition);
    }

    public function findAll(string $orderBy = 'id', string $orderDirection = 'asc'): \Illuminate\Database\Eloquent\Collection
    {
        return $this->coachEducationRepository->query()->orderBy($orderBy, $orderDirection)->get();
    }

    public function createEducation(array $data): string
    {
        // This is a complex method that would need full implementation
        // For now, creating a basic structure
        $educationId = $data['educationId'] ?? null;
        $coachId = $data['coachId'] ?? null;
        $seasonSportId = $data['seasonSportId'] ?? null;

        if (!$educationId && $coachId) {
            $education = $this->coachEducationRepository->create([
                'date' => $data['date'] ?? null,
                'module' => $data['module'] ?? null,
                'comment' => $data['comment'] ?? null,
                'hours' => $data['hours'] ?? null,
                'coach_id' => $coachId,
                'deleted' => false,
            ]);

            // Handle license types if provided
            if (isset($data['licenseB']) && $data['licenseB']) {
                $this->handleCoachLicense($coachId, 2, $education->id, $seasonSportId);
            }
            if (isset($data['licenseM']) && $data['licenseM']) {
                $this->handleCoachLicense($coachId, 1, $education->id, $seasonSportId);
            }
            if (isset($data['licenseT']) && $data['licenseT']) {
                $this->handleCoachLicense($coachId, 3, $education->id, $seasonSportId);
            }
        }

        return 'success';
    }

    protected function handleCoachLicense(int $coachId, int $coachLicenseTypeId, int $educationId, ?int $seasonSportId): void
    {
        $existingLicense = $this->coachLicenseService->findOne([
            'where' => [
                'coach_id' => $coachId,
                'coach_license_type_id' => $coachLicenseTypeId,
            ],
        ]);

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addYear()->format('Y-m-d');

        if ($existingLicense) {
            $this->coachLicenseService->update($existingLicense->id, [
                'deleted' => false,
                'end' => $endDate,
            ]);
        } else {
            $this->coachLicenseService->create([
                'coach_license_type_id' => $coachLicenseTypeId,
                'coach_id' => $coachId,
                'start' => $startDate,
                'end' => $endDate,
            ]);
        }

        $existingEducationLicense = CoachEducationLicenseType::where('coach_education_id', $educationId)
            ->where('coach_license_type_id', $coachLicenseTypeId)
            ->first();

        if (!$existingEducationLicense) {
            CoachEducationLicenseType::create([
                'coach_education_id' => $educationId,
                'coach_license_type_id' => $coachLicenseTypeId,
            ]);
        }
    }

    public function update(int $id, array $data): bool
    {
        return $this->coachEducationRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->coachEducationRepository->delete($id);
    }
}

