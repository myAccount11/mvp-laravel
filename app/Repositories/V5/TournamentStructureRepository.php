<?php

namespace App\Repositories\V5;

use App\Models\V5\TournamentStructure;
use App\Repositories\BaseRepository;

class TournamentStructureRepository extends BaseRepository
{
    public function __construct(TournamentStructure $model)
    {
        parent::__construct($model);
    }
}

