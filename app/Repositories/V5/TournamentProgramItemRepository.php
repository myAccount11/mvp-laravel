<?php

namespace App\Repositories\V5;

use App\Models\V5\TournamentProgramItem;
use App\Repositories\BaseRepository;

class TournamentProgramItemRepository extends BaseRepository
{
    public function __construct(TournamentProgramItem $model)
    {
        parent::__construct($model);
    }
}



