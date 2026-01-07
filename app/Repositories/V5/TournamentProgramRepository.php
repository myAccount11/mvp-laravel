<?php

namespace App\Repositories\V5;

use App\Models\V5\TournamentProgram;
use App\Repositories\BaseRepository;

class TournamentProgramRepository extends BaseRepository
{
    public function __construct(TournamentProgram $model)
    {
        parent::__construct($model);
    }
}

