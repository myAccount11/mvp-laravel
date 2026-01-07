<?php

namespace App\Services\V5;

use App\Models\V5\CoachLicense;
use App\Repositories\V5\CoachLicenseRepository;

class CoachLicenseService
{
    protected CoachLicenseRepository $coachLicenseRepository;

    public function __construct(CoachLicenseRepository $coachLicenseRepository)
    {
        $this->coachLicenseRepository = $coachLicenseRepository;
    }

    public function findOne(array $condition): ?CoachLicense
    {
        return $this->coachLicenseRepository->findOneBy($condition);
    }

    public function findAll(string $orderBy = 'id', string $orderDirection = 'asc'): \Illuminate\Database\Eloquent\Collection
    {
        return $this->coachLicenseRepository->query()->orderBy($orderBy, $orderDirection)->get();
    }

    public function create(array $data): CoachLicense
    {
        return $this->coachLicenseRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->coachLicenseRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->coachLicenseRepository->delete($id);
    }
}
