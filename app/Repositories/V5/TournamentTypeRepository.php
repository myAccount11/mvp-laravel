<?php

namespace App\Repositories\V5;

use App\Models\V5\TournamentType;
use App\Repositories\BaseRepository;

class TournamentTypeRepository extends BaseRepository
{
    public function __construct(TournamentType $model)
    {
        parent::__construct($model);
    }
}

