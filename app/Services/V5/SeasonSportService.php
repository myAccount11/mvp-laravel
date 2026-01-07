<?php

namespace App\Services\V5;

use App\Models\V5\SeasonSport;
use App\Repositories\V5\SeasonSportRepository;

class SeasonSportService
{
    protected SeasonSportRepository $seasonSportRepository;

    public function __construct(SeasonSportRepository $seasonSportRepository)
    {
        $this->seasonSportRepository = $seasonSportRepository;
    }

    public function findOne(array $condition): ?SeasonSport
    {
        return $this->seasonSportRepository->findOneBy($condition);
    }
}

