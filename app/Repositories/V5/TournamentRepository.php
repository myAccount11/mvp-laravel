<?php

namespace App\Repositories\V5;

use App\Models\V5\Tournament;
use App\Repositories\BaseRepository;

class TournamentRepository extends BaseRepository
{
    public function __construct(Tournament $model)
    {
        parent::__construct($model);
    }
}

