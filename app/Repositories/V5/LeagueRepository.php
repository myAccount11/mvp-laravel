<?php

namespace App\Repositories\V5;

use App\Models\V5\League;
use App\Repositories\BaseRepository;

class LeagueRepository extends BaseRepository
{
    public function __construct(League $model)
    {
        parent::__construct($model);
    }
}

