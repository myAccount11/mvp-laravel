<?php

namespace App\Repositories\V5;

use App\Models\V5\TournamentGroup;
use App\Repositories\BaseRepository;

class TournamentGroupRepository extends BaseRepository
{
    public function __construct(TournamentGroup $model)
    {
        parent::__construct($model);
    }
}

