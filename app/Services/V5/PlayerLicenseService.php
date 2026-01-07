<?php

namespace App\Services\V5;

use App\Models\V5\PlayerLicense;
use App\Repositories\V5\PlayerLicenseRepository;

class PlayerLicenseService
{
    protected PlayerLicenseRepository $playerLicenseRepository;

    public function __construct(PlayerLicenseRepository $playerLicenseRepository)
    {
        $this->playerLicenseRepository = $playerLicenseRepository;
    }

    public function findOne(array $condition): ?PlayerLicense
    {
        return $this->playerLicenseRepository->findOneBy($condition);
    }

    public function create(array $data): PlayerLicense
    {
        return $this->playerLicenseRepository->create($data);
    }

    public function updateByCondition(array $conditions, array $data): int
    {
        $query = $this->playerLicenseRepository->query();
        if (isset($conditions['where'])) {
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
            foreach ($conditions as $key => $value) {
                $query->where($key, $value);
            }
        }
        return $query->update($data);
    }
}

