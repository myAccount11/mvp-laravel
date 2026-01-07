<?php

namespace App\Repositories\V5;

use App\Models\V5\UserSeasonSport;
use App\Repositories\BaseRepository;

class UserSeasonSportRepository extends BaseRepository
{
    public function __construct(UserSeasonSport $model)
    {
        parent::__construct($model);
    }
}

