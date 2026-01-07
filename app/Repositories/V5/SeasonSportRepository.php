<?php

namespace App\Repositories\V5;

use App\Models\V5\SeasonSport;
use App\Repositories\BaseRepository;

class SeasonSportRepository extends BaseRepository
{
    public function __construct(SeasonSport $model)
    {
        parent::__construct($model);
    }
}

